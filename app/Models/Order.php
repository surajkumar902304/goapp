<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $primaryKey = 'order_id';
    protected $table = 'orders';
    protected $fillable = [
        'user_id',
        'total_amount',
        'wallet_discount',
        'coupon_discount',
        'status',
        'fulfillment_status',
        'user_company_address_id',
        'delivery_method_id',
        'vat',
        'total_paid',
        'product_total_amount',
        'delivery_instructions',
        'coupon_id',
        'pay_by_bank',
        'royalmail_order_identifier',
        'pushed_to_cnd_at',
        'cnd_status',
        'cnd_last_error',
        'payment_status',
        'payment_provider',
        'payment_reference',
        'sendcloud_parcel_id',
        'label_url',
    ];

    protected $casts = [
        'pay_by_bank'  => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class,'order_id','order_id');
    }

    public function userCompanyAddress()
    {
        return $this->belongsTo(UserCompanyAddress::class,'user_company_address_id','user_company_address_id');
    }

    public function deliveryMethod()
    {
        return $this->belongsTo(DeliveryMethod::class,'delivery_method_id','delivery_method_id');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class,'coupon_id','coupon_id');
    }

    public function receipts()
    {
        return $this->belongsTo(OrderFulfillment::class,'order_id','order_id');
    }

    public function fulfillments()
    {
        return $this->hasMany(\App\Models\OrderFulfillment::class, 'order_id')
            ->with(['items.orderItem.variant.product'])
            ->orderByDesc('order_fulfillment_id');
    }
}
