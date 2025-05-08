<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_Offer extends Model
{
    use HasFactory;
    protected $primaryKey = 'product_offer_id';
    protected $table = 'product__offers';
    protected $fillable = ['mvariant_id', 'product_deal_tag', 'product_offer'];
}
