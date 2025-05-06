<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mproduct extends Model
{
    use HasFactory;
    protected $primaryKey = 'mproduct_id';
    protected $table = 'mproducts';

    protected $fillable = [
        'mproduct_title', 
        'mproduct_image', 
        'mproduct_slug', 
        'status',
        'saleschannel',
        'mproduct_type_id',
        'mbrand_id',
        'mtags',
        'mproduct_desc',
        'product_deal_tag',
        'product_offer'
    ];

    protected $casts = [
        'mtags'=> 'array',
    ];

    public function mvariantsApi()
    {
        return $this->hasMany(Mvariant::class, 'mproduct_id', 'mproduct_id');
    }
    public function mvariants()
    {
        return $this->hasMany(Mvariant::class, 'mproduct_id', 'mproduct_id')
        ->join('mvariant_details', 'mvariant_details.mvariant_id','=','mvariants.mvariant_id')
        ->join('mstocks', 'mstocks.mvariant_id','=','mvariants.mvariant_id')
        ->select( 'mvariants.*', 'mvariant_details.options', 'mvariant_details.option_value', 'mstocks.quantity', 'mstocks.mlocation_id');
    }

    public function type()  { return $this->belongsTo(Mproduct_type::class, 'mproduct_type_id'); }
    public function brand() { return $this->belongsTo(Mbrand::class,        'mbrand_id'); }
    
    // public function mproduct_type()
    // {
    //     return $this->belongsTo(Mproduct_type::class, 'mproduct_type_id', 'mproduct_type_id')
    //     ->select(['mproduct_type_id', 'mproduct_type_name']);
    // }

    // public function mbrand()
    // {
    //     return $this->belongsTo(Mbrand::class, 'mbrand_id', 'mbrand_id')
    //     ->select(['mbrand_id', 'mbrand_name']);
    // }

}
