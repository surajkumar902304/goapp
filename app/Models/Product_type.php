<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_type extends Model
{
    use HasFactory;
    protected $primaryKey = 'product_type_id';
    protected $table = 'product_types';

    protected $fillable = ['product_type_id', 'product_type_name', 'product_type_status', 'shop_id'];
}
