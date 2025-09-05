<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponUsage extends Model
{
    use HasFactory;
    protected $primaryKey = 'coupon_usage_id';
    protected $table = 'coupon_usages';

    protected $fillable = [
        'coupon_id',
        'user_id',
        'used_count',
    ];

     public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id', 'coupon_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
