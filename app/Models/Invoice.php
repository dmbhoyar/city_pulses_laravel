<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'user_id', 'invoice_number', 'client_name', 'client_phone', 'client_email',
        'client_address', 'invoice_date', 'due_date', 'status',
        'subtotal', 'discount_amount', 'tax_amount', 'total', 'currency',
        'notes', 'terms',
        'business_name', 'business_phone', 'business_address', 'business_gstin',
        'bank_name', 'bank_account', 'bank_ifsc', 'bank_holder', 'payment_qr',
        'payment_received_at', 'payment_method', 'payment_note',
        'shared_at', 'shared_via',
    ];

    protected $casts = [
        'invoice_date'        => 'date',
        'due_date'            => 'date',
        'payment_received_at' => 'datetime',
        'shared_at'           => 'datetime',
        'subtotal'            => 'decimal:2',
        'discount_amount'     => 'decimal:2',
        'tax_amount'          => 'decimal:2',
        'total'               => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('sort_order');
    }

    public static function generateNumber(int $userId): string
    {
        $year  = now()->year;
        $count = static::where('user_id', $userId)->whereYear('created_at', $year)->count() + 1;
        return sprintf('INV-%d-%d-%03d', $year, $userId, $count);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'paid'      => ['label' => 'Paid',      'color' => '#16a34a', 'bg' => '#f0fdf4', 'border' => '#bbf7d0'],
            'sent'      => ['label' => 'Sent',      'color' => '#2563eb', 'bg' => '#eff6ff', 'border' => '#bfdbfe'],
            'cancelled' => ['label' => 'Cancelled', 'color' => '#6b7280', 'bg' => '#f9fafb', 'border' => '#e5e7eb'],
            default     => ['label' => 'Draft',     'color' => '#d97706', 'bg' => '#fffbeb', 'border' => '#fde68a'],
        };
    }

    public function isOverdue(): bool
    {
        return $this->due_date
            && $this->due_date->isPast()
            && !in_array($this->status, ['paid', 'cancelled']);
    }

    /** Group items by tax rate for GST summary table */
    public function taxSummary(): array
    {
        $groups = [];
        foreach ($this->items as $item) {
            $rate = (float) $item->tax_rate;
            $key  = (string) $rate;
            if (!isset($groups[$key])) {
                $groups[$key] = ['rate' => $rate, 'taxable' => 0, 'tax' => 0];
            }
            $taxable = (float) $item->quantity * (float) $item->unit_price;
            $groups[$key]['taxable'] += $taxable;
            $groups[$key]['tax']     += (float) $item->tax_amount;
        }
        return array_values($groups);
    }

    /** Group items by HSN/SAC code for detailed GST summary (like reference invoices) */
    public function taxSummaryByHsn(): array
    {
        $groups = [];
        foreach ($this->items as $item) {
            $hsn  = trim((string) $item->hsn_sac) ?: '—';
            $rate = (float) $item->tax_rate;
            $key  = $hsn . '_' . $rate;
            if (!isset($groups[$key])) {
                $groups[$key] = ['hsn' => $hsn, 'rate' => $rate, 'taxable' => 0, 'tax' => 0];
            }
            $taxable = (float) $item->quantity * (float) $item->unit_price;
            $groups[$key]['taxable'] += $taxable;
            $groups[$key]['tax']     += (float) $item->tax_amount;
        }
        return array_values($groups);
    }
}
