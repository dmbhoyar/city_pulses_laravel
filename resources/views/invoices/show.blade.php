@extends('layouts.app')

@section('title', 'Invoice '.$invoice->invoice_number)

@section('content')
@php
  $badge   = $invoice->status_badge;
  $overdue = $invoice->isOverdue();
  $taxSummary = $invoice->taxSummary();
  $printUrl   = route('invoices.print', $invoice);
  $shareUrl   = url()->current();
  $waText     = rawurlencode("Hello {$invoice->client_name}! Here is your invoice {$invoice->invoice_number}" .
    ($invoice->business_name ? " from {$invoice->business_name}" : '') .
    ".\n\nAmount: ₹" . number_format($invoice->total, 2) .
    "\nDate: " . optional($invoice->invoice_date)->format('d M Y') .
    "\n\nView Invoice: {$printUrl}");
@endphp
<style>
.inv-show-wrap{max-width:900px;margin:0 auto;padding:1.2rem 1rem 3rem}

/* Action bar */
.inv-action-bar{display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;margin-bottom:1.2rem}
.inv-back-link{font-size:.82rem;color:#b91c1c;text-decoration:none;margin-right:auto}
.inv-back-link:hover{text-decoration:underline}
.inv-act-btn{padding:.45rem .9rem;border-radius:7px;font-size:.8rem;font-weight:700;text-decoration:none;border:none;cursor:pointer;display:flex;align-items:center;gap:.35rem;transition:all .15s}
.inv-act-btn.outline{background:#fff;border:1px solid #d1d5db;color:#374151}
.inv-act-btn.outline:hover{background:#f3f4f6;border-color:#9ca3af}
.inv-act-btn.blue{background:#1e40af;color:#fff}
.inv-act-btn.blue:hover{background:#1d3a8a}
.inv-act-btn.green{background:#15803d;color:#fff}
.inv-act-btn.green:hover{background:#166534}
.inv-act-btn.wa{background:#25d366;color:#fff}
.inv-act-btn.wa:hover{background:#1da851}
.inv-act-btn.red{background:#dc2626;color:#fff}
.inv-act-btn.red:hover{background:#b91c1c}
.inv-act-btn.amber{background:#d97706;color:#fff}
.inv-act-btn.amber:hover{background:#b45309}

/* Invoice paper */
.inv-paper{background:#fff;border:1.5px solid #d1d5db;border-radius:10px;overflow:hidden;box-shadow:0 2px 14px rgba(0,0,0,.07)}
.inv-paper-head{background:#fff;border-bottom:2px solid #111;padding:1.2rem 1.8rem;display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem}
.inv-biz-left{display:flex;align-items:flex-start;gap:.9rem;flex:1}
.inv-biz-logo{width:78px;height:78px;object-fit:contain;border:1px solid #e5e7eb;border-radius:5px;flex-shrink:0}
.inv-biz-name{font-size:1.15rem;font-weight:800;text-transform:uppercase;letter-spacing:.3px;color:#111;margin-bottom:.22rem}
.inv-biz-sub{font-size:.77rem;color:#444;line-height:1.7}
.inv-meta-box{text-align:right;min-width:150px;flex-shrink:0}
.inv-inv-type{font-size:1.5rem;font-weight:900;text-transform:uppercase;letter-spacing:1px;color:#111;margin-bottom:.3rem}
.inv-inv-label{font-size:.62rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#999;margin-bottom:.05rem}
.inv-inv-num{font-size:1rem;font-weight:800;font-family:monospace;color:#111}
.inv-inv-date{font-size:.77rem;color:#555;margin-top:.25rem;line-height:1.7}

.inv-paper-body{padding:1.5rem 1.8rem}
.inv-two-col{display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.4rem}
.inv-sect-label{font-size:.65rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#9ca3af;margin-bottom:.4rem}
.inv-client-name{font-size:1rem;font-weight:700;color:#1a1208;margin-bottom:.2rem}
.inv-client-detail{font-size:.8rem;color:#6b7280;line-height:1.6}

/* Badge */
.inv-status-badge{display:inline-flex;align-items:center;gap:.3rem;padding:.3rem .75rem;border-radius:20px;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px}
.inv-overdue-tag{background:#fef2f2;color:#dc2626;border:1px solid #fecaca}

/* Items table */
.inv-items-tbl{width:100%;border-collapse:collapse;margin-bottom:1rem}
.inv-items-tbl th{background:#f9fafb;border-bottom:2px solid #e5e7eb;padding:.55rem .75rem;font-size:.72rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.4px;text-align:left}
.inv-items-tbl td{padding:.65rem .75rem;border-bottom:1px solid #f3f4f6;font-size:.875rem;color:#374151;vertical-align:top}
.inv-items-tbl tr:last-child td{border-bottom:none}
.inv-items-tbl .num{text-align:right}
.inv-item-desc{font-weight:600;color:#1a1208}
.inv-item-hsn{font-size:.7rem;color:#9ca3af;margin-top:.1rem;font-family:monospace}

/* Totals + summary */
.inv-bottom{display:grid;grid-template-columns:1fr auto;gap:1.5rem;align-items:start;margin-top:.75rem}
.inv-tax-summary table{width:100%;border-collapse:collapse;font-size:.78rem}
.inv-tax-summary th{background:#f9fafb;padding:.4rem .6rem;color:#6b7280;font-weight:700;border:1px solid #e5e7eb}
.inv-tax-summary td{padding:.35rem .6rem;border:1px solid #e5e7eb;color:#374151}
.inv-tax-summary-title{font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#9ca3af;margin-bottom:.35rem}
.inv-totals-box{min-width:220px}
.inv-tot-row{display:flex;justify-content:space-between;padding:.3rem 0;font-size:.875rem;border-bottom:1px solid #f3f4f6}
.inv-tot-row:last-child{border-bottom:none;font-size:1.05rem;font-weight:800;padding-top:.6rem;color:#1a1208}
.inv-tot-row span:first-child{color:#9ca3af}
.inv-tot-row span:last-child{font-weight:700}
.inv-grand-total{background:linear-gradient(135deg,#1a1208,#2d3748);color:#fff;border-radius:8px;padding:.75rem 1rem;margin-top:.5rem;display:flex;justify-content:space-between;align-items:center}
.inv-grand-total .label{font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:rgba(255,255,255,.65)}
.inv-grand-total .amount{font-size:1.3rem;font-weight:800;color:#fbbf24}

/* Notes / bank / footer */
.inv-paper-footer{background:#f9fafb;border-top:1px solid #ccc;padding:.9rem 1.8rem;display:grid;grid-template-columns:1fr 1fr 1fr;gap:0}
.inv-footer-cell{padding:.1rem .85rem}
.inv-footer-cell:first-child{padding-left:0}
.inv-footer-cell:last-child{padding-right:0}
.inv-footer-cell+.inv-footer-cell{border-left:1px solid #e5e7eb}
.inv-footer-sect-title{font-size:.63rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#9ca3af;margin-bottom:.35rem}
.inv-footer-text{font-size:.77rem;color:#555;line-height:1.7}
.inv-footer-bank dt{font-size:.68rem;color:#9ca3af;float:left;width:70px;clear:left}
.inv-footer-bank dd{font-size:.77rem;color:#333;font-weight:600;margin-left:70px;margin-bottom:.12rem}
.inv-sig-img{height:52px;max-width:140px;object-fit:contain;display:block;margin:.3rem 0}
.inv-sig-line{border-top:1px solid #e5e7eb;padding-top:.25rem;margin-top:.3rem;font-size:.7rem;color:#6b7280}

/* Payment track */
.inv-payment-box{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:.75rem 1rem;margin-bottom:1.2rem;display:flex;align-items:center;gap:.75rem;font-size:.85rem}
.inv-payment-box .icon{font-size:1.3rem}
.inv-payment-box strong{color:#15803d}

/* Modals */
.inv-modal-bg{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9998;display:none;align-items:center;justify-content:center}
.inv-modal-bg.open{display:flex}
.inv-modal{background:#fff;border-radius:12px;padding:1.5rem;width:90%;max-width:380px;box-shadow:0 20px 60px rgba(0,0,0,.25)}
.inv-modal h3{margin:0 0 .75rem;font-size:1rem;color:#1a1208}
.inv-modal label{font-size:.8rem;font-weight:700;color:#374151;display:block;margin-bottom:.25rem}
.inv-modal input,.inv-modal select{width:100%;border:1px solid #d1d5db;border-radius:6px;padding:.45rem .7rem;font-size:.875rem;margin-bottom:.65rem;outline:none;box-sizing:border-box}
.inv-modal-btns{display:flex;gap:.5rem;justify-content:flex-end}
.inv-modal-btns button{padding:.45rem 1rem;border-radius:6px;font-size:.82rem;font-weight:700;cursor:pointer;border:none}

@media(max-width:640px){
  .inv-paper-head{flex-direction:column}
  .inv-biz-left{flex-wrap:wrap}
  .inv-meta-box{text-align:left;min-width:unset}
  .inv-inv-type{font-size:1.1rem}
  .inv-two-col,.inv-bottom{grid-template-columns:1fr}
  .inv-totals-box{min-width:unset}
  .inv-paper-footer{grid-template-columns:1fr;padding:.75rem 1rem}
  .inv-footer-cell{padding:.6rem 0;border-left:none !important;border-top:1px solid #e5e7eb}
  .inv-footer-cell:first-child{border-top:none}
}
</style>

<div class="inv-show-wrap">

    {{-- Action bar --}}
    <div class="inv-action-bar">
        <a href="{{ route('invoices.index') }}" class="inv-back-link">← All Invoices</a>

        <a href="{{ route('invoices.print', $invoice) }}" target="_blank" class="inv-act-btn outline" title="Open print/PDF view">
            🖨 Print / PDF
        </a>
        <a href="https://wa.me/?text={{ $waText }}" target="_blank" rel="noopener" class="inv-act-btn wa" id="wa-share-btn">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
            Share WhatsApp
        </a>
        @if($invoice->status !== 'paid')
            <button onclick="document.getElementById('payment-modal').classList.add('open')" class="inv-act-btn green">
                ✓ Mark Paid
            </button>
        @endif
        <a href="{{ route('invoices.edit', $invoice) }}" class="inv-act-btn outline">✏️ Edit</a>
        <form method="POST" action="{{ route('invoices.destroy', $invoice) }}" onsubmit="return confirm('Delete this invoice permanently?')">
            @csrf @method('DELETE')
            <button type="submit" class="inv-act-btn red">🗑 Delete</button>
        </form>
    </div>

    @if(session('success'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;padding:.7rem 1rem;border-radius:8px;margin-bottom:1rem;font-size:.875rem;font-weight:600">
            ✓ {{ session('success') }}
        </div>
    @endif

    {{-- Payment received banner --}}
    @if($invoice->status === 'paid')
        <div class="inv-payment-box">
            <span class="icon">✅</span>
            <div>
                <strong>Payment Received</strong> via {{ ucfirst($invoice->payment_method ?? 'cash') }}
                @if($invoice->payment_received_at) — {{ $invoice->payment_received_at->format('d M Y, h:i A') }} @endif
                @if($invoice->payment_note) <br><span style="color:#374151">{{ $invoice->payment_note }}</span> @endif
            </div>
        </div>
    @endif

    {{-- ══ Invoice Paper ══ --}}
    <div class="inv-paper">

        {{-- Header --}}
        <div class="inv-paper-head">
            <div class="inv-biz-left">
                @if($profile->business_logo)
                    <img src="{{ asset('storage/'.$profile->business_logo) }}" alt="Logo" class="inv-biz-logo">
                @endif
                <div>
                    <div class="inv-biz-name">{{ $profile->business_name ?: ($invoice->business_name ?: 'Your Business') }}</div>
                    <div class="inv-biz-sub">
                        @php $bizAddr = $profile->business_address ?: $invoice->business_address; @endphp
                        @if($bizAddr) {{ str_replace("\n", ', ', trim($bizAddr)) }}<br> @endif
                        @if($profile->business_phone ?: $invoice->business_phone)
                            Phone: <strong>{{ $profile->business_phone ?: $invoice->business_phone }}</strong>
                            @if($profile->business_email) &nbsp;·&nbsp; Email: <strong>{{ $profile->business_email }}</strong> @endif
                            <br>
                        @elseif($profile->business_email)
                            Email: <strong>{{ $profile->business_email }}</strong><br>
                        @endif
                        @if($profile->gstin ?: $invoice->business_gstin)
                            GSTIN: <strong>{{ $profile->gstin ?: $invoice->business_gstin }}</strong> &nbsp;·&nbsp;
                        @endif
                        State: <strong>27-Maharashtra</strong>
                    </div>
                </div>
            </div>
            <div class="inv-meta-box">
                <div class="inv-inv-type">{{ $invoice->status === 'draft' ? 'Estimate' : 'Invoice' }}</div>
                <div class="inv-inv-label">No.</div>
                <div class="inv-inv-num">{{ $invoice->invoice_number }}</div>
                <div class="inv-inv-date">
                    Date: <strong>{{ optional($invoice->invoice_date)->format('d M Y') }}</strong><br>
                    @if($invoice->due_date)
                        Due: <strong style="{{ $overdue ? 'color:#dc2626' : '' }}">{{ $invoice->due_date->format('d M Y') }}</strong><br>
                    @endif
                    Status: <strong>
                        @if($overdue && $invoice->status !== 'paid')
                            <span style="color:#dc2626">OVERDUE</span>
                        @else
                            {{ strtoupper($invoice->status) }}
                        @endif
                    </strong>
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="inv-paper-body">

            {{-- Bill To / Issued By --}}
            <div class="inv-two-col">
                <div>
                    <div class="inv-sect-label">Bill To</div>
                    <div class="inv-client-name">{{ $invoice->client_name }}</div>
                    <div class="inv-client-detail">
                        @if($invoice->client_phone) 📞 {{ $invoice->client_phone }}<br> @endif
                        @if($invoice->client_email) ✉ {{ $invoice->client_email }}<br> @endif
                        @if($invoice->client_address) 📍 {{ $invoice->client_address }} @endif
                    </div>
                </div>
                @if($invoice->shared_at)
                <div>
                    <div class="inv-sect-label">Shared</div>
                    <div style="font-size:.82rem;color:#6b7280">
                        Via {{ ucfirst($invoice->shared_via ?? 'whatsapp') }}<br>
                        {{ $invoice->shared_at->format('d M Y') }}
                    </div>
                </div>
                @endif
            </div>

            {{-- Items Table --}}
            <table class="inv-items-tbl">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Description</th>
                        <th>HSN/SAC</th>
                        <th class="num">Qty</th>
                        <th class="num">Price</th>
                        <th class="num">GST</th>
                        <th class="num">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $i => $item)
                    <tr>
                        <td style="color:#9ca3af;font-size:.78rem">{{ $i+1 }}</td>
                        <td>
                            <div class="inv-item-desc">{{ $item->description }}</div>
                        </td>
                        <td>
                            @if($item->hsn_sac)<span class="inv-item-hsn">{{ $item->hsn_sac }}</span>@else<span style="color:#d1d5db">—</span>@endif
                        </td>
                        <td class="num">{{ rtrim(rtrim((string)$item->quantity,'0'),'.') }} {{ $item->unit }}</td>
                        <td class="num">₹{{ number_format($item->unit_price,2) }}</td>
                        <td class="num">{{ $item->tax_rate > 0 ? $item->tax_rate.'%' : '—' }}</td>
                        <td class="num" style="font-weight:700;color:#1a1208">₹{{ number_format($item->amount,2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Bottom: Tax Summary + Totals --}}
            <div class="inv-bottom">
                <div>
                    @if(count($taxSummary) > 0 && collect($taxSummary)->sum('tax') > 0)
                        <div class="inv-tax-summary">
                            <div class="inv-tax-summary-title">Tax Summary</div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Rate</th>
                                        <th>Taxable (₹)</th>
                                        <th>CGST (₹)</th>
                                        <th>SGST (₹)</th>
                                        <th>Total Tax (₹)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($taxSummary as $row)
                                    @if($row['tax'] > 0)
                                    <tr>
                                        <td>{{ $row['rate'] }}%</td>
                                        <td>{{ number_format($row['taxable'],2) }}</td>
                                        <td>{{ number_format($row['tax']/2,2) }}</td>
                                        <td>{{ number_format($row['tax']/2,2) }}</td>
                                        <td style="font-weight:700">{{ number_format($row['tax'],2) }}</td>
                                    </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                <div class="inv-totals-box">
                    <div class="inv-tot-row">
                        <span>Subtotal</span>
                        <span>₹{{ number_format($invoice->subtotal,2) }}</span>
                    </div>
                    @if($invoice->tax_amount > 0)
                    <div class="inv-tot-row">
                        <span>GST / Tax</span>
                        <span>₹{{ number_format($invoice->tax_amount,2) }}</span>
                    </div>
                    @endif
                    @if($invoice->discount_amount > 0)
                    <div class="inv-tot-row">
                        <span>Discount</span>
                        <span style="color:#dc2626">- ₹{{ number_format($invoice->discount_amount,2) }}</span>
                    </div>
                    @endif
                    <div class="inv-grand-total">
                        <span class="label">Total Due</span>
                        <span class="amount">₹{{ number_format($invoice->total,2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer: 3-column like professional invoice references --}}
        <div class="inv-paper-footer">

            {{-- Column 1: Terms & Notes --}}
            <div class="inv-footer-cell">
                @if($invoice->terms)
                    <div class="inv-footer-sect-title">Terms &amp; Conditions</div>
                    <div class="inv-footer-text">{{ $invoice->terms }}</div>
                @endif
                @if($invoice->notes)
                    <div class="inv-footer-sect-title" @if($invoice->terms) style="margin-top:.6rem" @endif>Notes</div>
                    <div class="inv-footer-text">{{ $invoice->notes }}</div>
                @endif
                @if(!$invoice->terms && !$invoice->notes)
                    <div class="inv-footer-sect-title">Terms &amp; Conditions</div>
                    <div class="inv-footer-text" style="color:#d1d5db;font-style:italic">—</div>
                @endif
            </div>

            {{-- Column 2: Bank Details + QR --}}
            <div class="inv-footer-cell">
                <div class="inv-footer-sect-title">Bank Details</div>
                @if($invoice->bank_name || $invoice->bank_account || $profile->payment_qr)
                    <div style="display:flex;gap:.75rem;align-items:flex-start">
                        @if($profile->payment_qr)
                            <div style="flex-shrink:0">
                                <img src="{{ asset('storage/'.$profile->payment_qr) }}"
                                     alt="Payment QR"
                                     style="width:72px;height:72px;object-fit:contain;border:1px solid #e5e7eb;padding:2px;background:#fff">
                            </div>
                        @endif
                        <dl class="inv-footer-bank" style="margin:0">
                            @if($invoice->bank_name)<dt>Name</dt><dd>{{ $invoice->bank_name }}</dd>@endif
                            @if($invoice->bank_account)<dt>A/C No.</dt><dd>{{ $invoice->bank_account }}</dd>@endif
                            @if($invoice->bank_ifsc)<dt>IFSC</dt><dd>{{ $invoice->bank_ifsc }}</dd>@endif
                            @if($invoice->bank_holder)<dt>Holder</dt><dd>{{ $invoice->bank_holder }}</dd>@endif
                        </dl>
                    </div>
                @else
                    <div class="inv-footer-text" style="color:#d1d5db;font-style:italic">No bank details added</div>
                @endif
            </div>

            {{-- Column 3: Authorized Signatory --}}
            <div class="inv-footer-cell">
                <div class="inv-footer-sect-title">For {{ $profile->business_name ?: ($invoice->business_name ?: 'Your Business') }}</div>
                @if($profile->signature)
                    <img src="{{ asset('storage/'.$profile->signature) }}" alt="Signature" class="inv-sig-img">
                @else
                    <div style="height:52px;border:1px dashed #e5e7eb;border-radius:5px;display:flex;align-items:flex-end;justify-content:center;padding-bottom:.3rem;margin:.3rem 0">
                        <span style="font-size:.68rem;color:#d1d5db">Authorized Signatory</span>
                    </div>
                @endif
                <div class="inv-sig-line">Authorized Signatory</div>
            </div>

        </div>
    </div>
</div>

{{-- ── Mark Payment Modal ── --}}
<div class="inv-modal-bg" id="payment-modal">
    <div class="inv-modal">
        <h3>✓ Mark Payment Received</h3>
        <form method="POST" action="{{ route('invoices.payment', $invoice) }}">
            @csrf
            <label>Payment Method</label>
            <select name="payment_method">
                <option value="cash">Cash</option>
                <option value="upi">UPI / GPay / PhonePe</option>
                <option value="bank_transfer">Bank Transfer / NEFT</option>
                <option value="cheque">Cheque</option>
                <option value="card">Card</option>
                <option value="other">Other</option>
            </select>
            <label>Note (optional)</label>
            <input type="text" name="payment_note" placeholder="e.g. UPI ref #12345">
            <div class="inv-modal-btns">
                <button type="button" onclick="document.getElementById('payment-modal').classList.remove('open')"
                    style="background:#f3f4f6;color:#374151">Cancel</button>
                <button type="submit" style="background:#15803d;color:#fff">✓ Confirm Paid</button>
            </div>
        </form>
    </div>
</div>

<script>
// Track WhatsApp share
document.getElementById('wa-share-btn')?.addEventListener('click', function() {
    fetch('{{ route('invoices.share', $invoice) }}', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json'},
        body: JSON.stringify({via: 'whatsapp'})
    });
});
// Close modal on background click
document.getElementById('payment-modal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('open');
});
</script>
@endsection
