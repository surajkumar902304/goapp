<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mcategory extends Model
{
    use HasFactory;
    protected $primaryKey = 'mcat_id';
    protected $table = 'mcategories';

    protected $fillable = ['mcat_name','main_mcat_id'];

    public function mainCategory()
    {
        return $this->belongsTo(MainCategory::class, 'main_mcat_id', 'main_mcat_id');
    }

    // category api
    public function subcategories()
    {
        return $this->hasMany(Msubcategory::class, 'mcat_id', 'mcat_id');
    }
}
