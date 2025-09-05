<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeSmallBanner extends Model
{
    use HasFactory;
    protected $primaryKey = 'home_small_banner_id';
    protected $table = 'home_small_banners';

    protected $fillable = ['main_mcat_id', 'mcat_id', 'msubcat_id', 'mproduct_id', 'home_small_banner_name','home_small_banner_image','home_small_banner_position'];

    public function maincategory() 
    {
        return $this->belongsTo(Mcategory::class,    'main_mcat_id',    'main_mcat_id');
    }
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
