<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewProduct extends Model
{
    use HasFactory;
    protected $primaryKey = 'new_product_id';
    protected $table = 'new_products';
    protected $fillable = ['mvariant_id'];

    public function variant()
    {
        return $this->belongsTo(Mvariant::class, 'mvariant_id')->with('product');         
    }
}
