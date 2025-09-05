<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Msubcategory extends Model
{
    use HasFactory;
    protected $primaryKey = 'msubcat_id';
    protected $table = 'msubcategories';

    protected $fillable = ['mcat_id', 'msubcat_name', 'msubcat_slug', 'msubcat_image', 'msubcat_tag', 'msubcat_publish', 
        'offer_name', 'start_time', 'end_time', 'msubcat_type', 'product_ids', 'logical_operator', 'status'
    ];

    protected $casts = [
        'msubcat_publish' => 'array',
        'product_ids' => 'array',
        'status' => 'boolean',
    ];
    // sub-category list
    public function category()
    {
        return $this->belongsTo(Mcategory::class, 'mcat_id', 'mcat_id');
    }
    
    public function products()
    {
        return $this->hasMany(Mproduct::class, 'mproduct_id', 'product_ids');
    }

    public function autos()
    {
        return $this->hasMany(Mcollection_auto::class, 'msubcat_id', 'msubcat_id')
            ->select('msubcat_id','field_id','query_id','value','logical_operator');
    }
}
