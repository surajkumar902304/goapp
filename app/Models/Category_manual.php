<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category_manual extends Model
{
    use HasFactory;

    protected $table = 'category_manuals';
    protected $primaryKey = 'category_manual_id';

    protected $fillable = [
        'cat_id', 'product_ids'
    ];

    protected $casts = [
        'product_ids' => 'array',
    ];
}
