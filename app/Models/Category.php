<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';
    protected $primaryKey = 'cat_id';

    protected $fillable = [
        'cat_title', 'cat_desc', 'cat_slug', 'cat_image', 'cat_type', 'shop_id'
    ];

    public function catrule()
    {
        return $this->hasMany(Category_auto::class, 'cat_id', 'cat_id')
            ->join('fields', 'category_autos.field_id', '=', 'fields.field_id')
            ->join('queries', 'category_autos.query_id', '=', 'queries.query_id')
            ->select('category_autos.*', 'fields.field_name', 'queries.query_name');
    }

    public function manualRules()
    {
        return $this->hasOne(Category_manual::class, 'cat_id', 'cat_id');
    }
}
