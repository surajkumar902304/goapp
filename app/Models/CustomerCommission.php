<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerCommission extends Model
{
    use HasFactory;
    protected $primaryKey = 'customer_commission_id';
    protected $table = 'customer_commissions';

    protected $fillable = ['rep_id', 'total_commission'];
}
