<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mvariant extends Model
{
    use HasFactory;
    protected $primaryKey = 'mvariant_id';
    protected $table = 'mvariants';

    protected $fillable = [
        'sku', 
        'mvariant_image', 
        'price', 
        'compare_price',
        'cost_price',
        'mproduct_id',
        'barcode',
        'weight',
        'weightunit',
        'taxable',
        'isvalidatedetails'
    ];

    public function product()
    {
        return $this->belongsTo(Mproduct::class,'mproduct_id','mproduct_id');
    }
    public function mvariantDetail()
    {
        return $this->hasOne(Mvariant_detail::class, 'mvariant_id', 'mvariant_id')
        ->select(['mvariant_detail_id', 'mvariant_id', 'options', 'option_value']);;
    }
    public function mstock()
    {
        return $this->hasOne(Mstock::class, 'mvariant_id', 'mvariant_id')
        ->select(['mstock_id', 'quantity', 'mlocation_id', 'mvariant_id']);
    }

    public function productoffer()
    {
        return $this->hasOne(Product_Offer::class, 'mvariant_id', 'mvariant_id');
    }

    public function details()
    {
        return $this->hasMany(Mvariant_detail::class, 'mvariant_id', 'mvariant_id');
    }

}
