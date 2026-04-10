<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponRedemption extends Model
{
    use HasFactory;

    const STATUSES = ['pending', 'approved', 'on_the_way', 'delivered', 'rejected'];

    const STATUS_LABELS = [
        'pending'    => 'Pending',
        'approved'   => 'Approved',
        'on_the_way' => 'On the Way',
        'delivered'  => 'Delivered',
        'rejected'   => 'Rejected',
    ];

    const STATUS_COLORS = [
        'pending'    => '#f59e0b',
        'approved'   => '#3b82f6',
        'on_the_way' => '#8b5cf6',
        'delivered'  => '#10b981',
        'rejected'   => '#ef4444',
    ];

    protected $fillable = [
        'user_id',
        'offer_id',
        'coupon_ref_id',
        'coupon_code',
        'store',
        'title',
        'redeemed_at',
        'status',
        'admin_notes',
        'processed_at',
        // Delivery address (product orders)
        'delivery_name',
        'delivery_phone',
        'delivery_address1',
        'delivery_address2',
        'delivery_city',
        'delivery_state',
        'delivery_pincode',
        'delivery_landmark',
    ];

    public function hasDeliveryAddress(): bool
    {
        return !empty($this->delivery_address1) && !empty($this->delivery_city);
    }

    public function getDeliveryAddressStringAttribute(): string
    {
        $parts = array_filter([
            $this->delivery_address1,
            $this->delivery_address2,
            $this->delivery_landmark ? 'Near ' . $this->delivery_landmark : null,
            $this->delivery_city,
            $this->delivery_state,
            $this->delivery_pincode,
        ]);
        return implode(', ', $parts);
    }

    protected $casts = [
        'redeemed_at'  => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function offer()
    {
        return $this->belongsTo(Update::class, 'offer_id');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_ref_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? '#6b7280';
    }
}
