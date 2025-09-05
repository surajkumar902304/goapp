<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderCommission extends Model
{
    use HasFactory;
    protected $primaryKey = 'order_commission_id';
    protected $table = 'order_commissions';

    protected $fillable = ['order_id', 'user_id', 'rep_id', 'product_total', 'commission_percent', 'commission_amount'];
}
