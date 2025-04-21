<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $primaryKey = 'brand_id';
    protected $table = 'brands';

    protected $fillable = ['brand_id', 'brand_name', 'brand_status', 'shop_id', 'brand_image'];
}
