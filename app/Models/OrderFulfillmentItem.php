<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderFulfillmentItem extends Model
{
    use HasFactory;
    protected $primaryKey = 'order_fulfillment_item_id';
    protected $table = 'order_fulfillment_items';
    protected $fillable = ['order_fulfillment_id','order_item_id','quantity'];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }
}
