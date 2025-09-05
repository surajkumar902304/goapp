<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    protected $primaryKey = 'order_item_id';
    protected $table = 'order_items';
    protected $fillable = [
        'order_id',
        'mvariant_id',
        'quantity',
        'fulfilled_quantity',
        'unit_price',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function variant()
    {
        return $this->belongsTo(Mvariant::class, 'mvariant_id', 'mvariant_id');
    }

}
