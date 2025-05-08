<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart_item extends Model
{
    use HasFactory;
    protected $primaryKey = 'cart_item_id';
    protected $table = 'cart_items';

    protected $fillable = ['user_id', 'mproduct_id', 'quantity','status'];

}
