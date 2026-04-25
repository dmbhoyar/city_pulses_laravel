<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\BusinessProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user->isShopowner() && !$user->isServiceProvider() && !$user->isSuperadmin()) {
                abort(403, 'Invoices are available for shop owners and service providers only.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $user  = auth()->user();
        $query = Invoice::where('user_id', $user->id)->with('items');

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('client_name', 'like', "%$search%")
                  ->orWhere('invoice_number', 'like', "%$search%")
                  ->orWhere('client_phone', 'like', "%$search%");
            });
        }

        $invoices = $query->latest()->paginate(15)->withQueryString();

        $uid = $user->id;
        $stats = [
            'total'        => Invoice::where('user_id', $uid)->count(),
            'draft'        => Invoice::where('user_id', $uid)->where('status', 'draft')->count(),
            'sent'         => Invoice::where('user_id', $uid)->where('status', 'sent')->count(),
            'paid'         => Invoice::where('user_id', $uid)->where('status', 'paid')->count(),
            'total_earned' => (float) Invoice::where('user_id', $uid)->where('status', 'paid')->sum('total'),
        ];

        return view('invoices.index', compact('invoices', 'stats'));
    }

    public function create()
    {
        $user    = auth()->user();
        $shop    = $user->shops()->first() ?? $user->shop;
        $profile = BusinessProfile::forUser($user->id);
        return view('invoices.form', compact('user', 'shop', 'profile'));
    }

    public function store(Request $request)
    {
        $this->validateForm($request);

        $user    = auth()->user();
        $invoice = null;

        $profile = BusinessProfile::forUser($user->id);

        DB::transaction(function () use ($request, $user, $profile, &$invoice) {
            $invoice = Invoice::create(array_merge(
                $this->invoiceFields($request),
                $this->bankSnapshot($request, $profile),
                [
                    'user_id'        => $user->id,
                    'invoice_number' => Invoice::generateNumber($user->id),
                    'status'         => 'draft',
                    'currency'       => 'INR',
                    'subtotal'       => 0,
                    'discount_amount'=> 0,
                    'tax_amount'     => 0,
                    'total'          => 0,
                ]
            ));
            $this->syncItems($invoice, $request->input('items', []));
        });

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice created successfully!');
    }

    public function show(Invoice $invoice)
    {
        $this->gate($invoice);
        $invoice->load('items');
        $profile = BusinessProfile::forUser($invoice->user_id);
        return view('invoices.show', compact('invoice', 'profile'));
    }

    public function edit(Invoice $invoice)
    {
        $this->gate($invoice);
        $invoice->load('items');
        $user    = auth()->user();
        $shop    = $user->shops()->first() ?? $user->shop;
        $profile = BusinessProfile::forUser($user->id);
        return view('invoices.form', compact('invoice', 'user', 'shop', 'profile'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $this->gate($invoice);
        $this->validateForm($request);

        $profile = BusinessProfile::forUser($invoice->user_id);

        DB::transaction(function () use ($request, $invoice, $profile) {
            $invoice->update(array_merge(
                $this->invoiceFields($request),
                $this->bankSnapshot($request, $profile)
            ));
            $this->syncItems($invoice, $request->input('items', []));
        });

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice updated!');
    }

    public function destroy(Invoice $invoice)
    {
        $this->gate($invoice);
        // Clean up stored QR image
        if ($invoice->payment_qr) {
            Storage::disk('public')->delete($invoice->payment_qr);
        }
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted.');
    }

    public function markPayment(Request $request, Invoice $invoice)
    {
        $this->gate($invoice);
        $request->validate([
            'payment_method' => 'nullable|string|max:50',
            'payment_note'   => 'nullable|string|max:255',
        ]);

        $invoice->update([
            'status'              => 'paid',
            'payment_received_at' => now(),
            'payment_method'      => $request->input('payment_method', 'cash'),
            'payment_note'        => $request->payment_note,
        ]);

        return back()->with('success', 'Payment marked as received!');
    }

    public function markShared(Request $request, Invoice $invoice)
    {
        $this->gate($invoice);

        $invoice->update([
            'status'     => $invoice->status === 'draft' ? 'sent' : $invoice->status,
            'shared_at'  => now(),
            'shared_via' => $request->input('via', 'whatsapp'),
        ]);

        return response()->json(['ok' => true]);
    }

    public function printView(Invoice $invoice)
    {
        $this->gate($invoice);
        $invoice->load('items');
        $profile = BusinessProfile::forUser($invoice->user_id);
        return view('invoices.print', compact('invoice', 'profile'));
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function gate(Invoice $invoice): void
    {
        if ($invoice->user_id !== auth()->id() && !auth()->user()->isSuperadmin()) {
            abort(403);
        }
    }

    private function validateForm(Request $request): void
    {
        $request->validate([
            'client_name'          => 'required|string|max:150',
            'client_phone'         => 'nullable|string|max:20',
            'client_email'         => 'nullable|email|max:150',
            'invoice_date'         => 'required|date',
            'due_date'             => 'nullable|date|after_or_equal:invoice_date',
            'items'                => 'required|array|min:1',
            'items.*.description'  => 'required|string|max:255',
            'items.*.quantity'     => 'required|numeric|min:0.001',
            'items.*.unit_price'   => 'required|numeric|min:0',
            'items.*.tax_rate'     => 'nullable|numeric|min:0|max:100',
        ]);
    }

    private function invoiceFields(Request $request): array
    {
        return [
            'client_name'      => $request->client_name,
            'client_phone'     => $request->client_phone,
            'client_email'     => $request->client_email,
            'client_address'   => $request->client_address,
            'invoice_date'     => $request->invoice_date,
            'due_date'         => $request->due_date ?: null,
            'notes'            => $request->notes,
            'terms'            => $request->terms,
            'business_name'    => $request->business_name,
            'business_phone'   => $request->business_phone,
            'business_address' => $request->business_address,
            'business_gstin'   => $request->business_gstin,
        ];
    }

    /** Resolve pre-fill values for the invoice form from business profile + shop fallback */
    public static function resolveBusinessDefaults(BusinessProfile $profile, $shop): array
    {
        return [
            'biz_name'    => $profile->business_name  ?: (optional($shop)->name    ?? ''),
            'biz_phone'   => $profile->business_phone  ?: (optional($shop)->phone   ?? ''),
            'biz_email'   => $profile->business_email  ?: '',
            'biz_address' => $profile->business_address ?: (optional($shop)->address ?? ''),
            'biz_gstin'   => $profile->gstin            ?? '',
        ];
    }

    /** Snapshot bank details: prefer per-invoice form values, fall back to profile */
    private function bankSnapshot(Request $request, BusinessProfile $profile): array
    {
        return [
            'bank_name'    => $request->input('bank_name')    ?: $profile->bank_name,
            'bank_account' => $request->input('bank_account') ?: $profile->bank_account,
            'bank_ifsc'    => $request->input('bank_ifsc')    ?: $profile->bank_ifsc,
            'bank_holder'  => $request->input('bank_holder')  ?: $profile->bank_holder,
        ];
    }

    private function syncItems(Invoice $invoice, array $items): void
    {
        $invoice->items()->delete();

        $subtotal = 0;
        $taxTotal = 0;

        foreach ($items as $i => $row) {
            if (empty(trim($row['description'] ?? ''))) {
                continue;
            }
            $qty     = (float) ($row['quantity'] ?? 1);
            $price   = (float) ($row['unit_price'] ?? 0);
            $taxRate = (float) ($row['tax_rate'] ?? 0);
            $base    = round($qty * $price, 2);
            $taxAmt  = round($base * $taxRate / 100, 2);

            InvoiceItem::create([
                'invoice_id'  => $invoice->id,
                'description' => $row['description'],
                'hsn_sac'     => $row['hsn_sac'] ?? null,
                'quantity'    => $qty,
                'unit'        => $row['unit'] ?? null,
                'unit_price'  => $price,
                'tax_rate'    => $taxRate,
                'tax_amount'  => $taxAmt,
                'amount'      => $base + $taxAmt,
                'sort_order'  => $i,
            ]);

            $subtotal += $base;
            $taxTotal += $taxAmt;
        }

        $invoice->update([
            'subtotal'   => round($subtotal, 2),
            'tax_amount' => round($taxTotal, 2),
            'total'      => round($subtotal + $taxTotal, 2),
        ]);
    }
}
