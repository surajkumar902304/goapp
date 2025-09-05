<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeRoundBanner extends Model
{
    use HasFactory;
    protected $primaryKey = 'home_round_banner_id';
    protected $table = 'home_round_banners';

    protected $fillable = ['mbrand_id', 'main_mcat_id', 'mcat_id', 'msubcat_id', 'mproduct_id', 'home_round_banner_name','home_round_banner_image', 'home_round_banner_position'];
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