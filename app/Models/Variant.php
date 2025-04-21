<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    use HasFactory;
    protected $primaryKey = 'variant_id';
    protected $table = 'variants';

    protected $fillable = [
        'sku', 'qty', 'price', 'product_id', 'product_type_id', 'brand_id'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function details()
    {
        return $this->hasMany(Variant_detail::class, 'variant_id', 'variant_id');
    }

    public function options()
{
    return $this->belongsToMany(Variant_detail::class, 'variant_details', 'variant_id', 'variant_id')
                ->withPivot('value');
}

    
}
