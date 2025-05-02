<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Browsebanner extends Model
{
    use HasFactory;
    protected $primaryKey = 'browsebanner_id';
    protected $table = 'browsebanners';

    protected $fillable = ['mcat_id', 'msubcat_id', 'mproduct_id', 'browsebanner_name','browsebanner_image','browsebanner_position'];

    public function category() 
    {
        return $this->belongsTo(Mcategory::class,    'mcat_id',    'mcat_id');
    }

    public function subcategory() 
    {
        return $this->belongsTo(Msubcategory::class, 'msubcat_id', 'msubcat_id');
    }

    public function product()   
    {
        return $this->belongsTo(Mproduct::class,     'mproduct_id','mproduct_id');
    }
}
