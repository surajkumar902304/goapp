<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $primaryKey = 'product_id';
    protected $table = 'products';

    protected $fillable = ['product_title','product_slug' , 'product_image', 'shop_id'];

    public function variants()
    {
        return $this->hasOne(Variant::class, 'product_id', 'product_id')
        ->join('variant_details','variant_details.variant_id','=','variants.variant_id')
        ->join('product_types','product_types.product_type_id','=','variants.product_type_id')
        ->join('brands','brands.brand_id','=','variants.brand_id');
    }

    public function variants_index()
{
    return $this->hasOne(Variant::class, 'product_id', 'product_id')
        ->join('variant_details', 'variant_details.variant_id', '=', 'variants.variant_id')
        ->join('product_types', 'product_types.product_type_id', '=', 'variants.product_type_id')
        ->join('brands', 'brands.brand_id', '=', 'variants.brand_id')
        ->join('option_names', function ($join) {
            $join->whereRaw("FIND_IN_SET(option_names.option_id, REPLACE(REPLACE(REPLACE(variant_details.option_ids, '[', ''), ']', ''), '\"', ''))");
        })
        ->select(
            'variants.*',
            'variant_details.*',
            'product_types.*',
            'brands.*',
            'option_names.option_name'
        );
}

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tags', 'product_id', 'tag_id');
    }

    public function atags()
    {
        return $this->hasMany(Product_tag::class,'product_id','product_id')
        ->join('tags','tags.tag_id','=','product_tags.tag_id');
    }

}
