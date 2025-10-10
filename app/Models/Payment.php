<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $primaryKey = 'payment_id';
    protected $table = 'payments';

    protected $fillable = [
        'order_id','user_id','provider','payment_intent_id','payment_method_id',
        'customer_id','currency','amount','status','receipt_email','description',
        'metadata','raw_payload',
    ];

    protected $casts = [
        'metadata' => 'array',
        'raw_payload' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

}
