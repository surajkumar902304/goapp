<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderFulfillment extends Model
{
    use HasFactory;
    protected $primaryKey = 'order_fulfillment_id';
    protected $table = 'order_fulfillments';
    protected $fillable = ['order_id','tracking_id','shipping_courier','fulfilled_at'];

    public function items()
    {
        return $this->hasMany(OrderFulfillmentItem::class, 'order_fulfillment_id');
    }
}
