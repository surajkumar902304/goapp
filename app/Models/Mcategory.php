<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mcategory extends Model
{
    use HasFactory;
    protected $primaryKey = 'mcat_id';
    protected $table = 'mcategories';

    protected $fillable = ['mcat_name'];
    // category api
    public function subcategories()
    {
        return $this->hasMany(Msubcategory::class, 'mcat_id', 'mcat_id');
    }
}
