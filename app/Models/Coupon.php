<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;
    protected $primaryKey = 'coupon_id';
    protected $table = 'coupons';

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'expires_at',
        'usage_limit',
        'per_user_limit',
        'min_cart_value',
        'is_active',
    ];

    protected $casts = [
        'expires_at' => 'date:Y-m-d',
        'is_active'  => 'boolean',
    ];

    public function usages()
    {
        return $this->hasMany(CouponUsage::class, 'coupon_id', 'coupon_id');
    }

    public function isValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->expires_at && Carbon::now()->gt($this->expires_at)) {
            return false;
        }

        return true;
    }

    public function totalUsed(): int
    {
        return $this->usages()->sum('used_count');
    }

    public function mainCategory()
    {
        return $this->belongsTo(MainCategory::class, 'main_mcat_id', 'main_mcat_id');
    }

}
