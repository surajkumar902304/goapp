<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainCategory extends Model
{
    use HasFactory;
    protected $primaryKey = 'main_mcat_id';
    protected $table = 'main_categories';

    protected $fillable = ['main_mcat_name'];
    // category api
    public function categories()
    {
        return $this->hasMany(Mcategory::class, 'main_mcat_id');
    }

}
