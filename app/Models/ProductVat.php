<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVat extends Model
{
    use HasFactory;
    protected $primaryKey = 'product_vat_id';
    protected $table = 'product_vats';
    protected $fillable = ['product_vat', 'is_active'];
    protected $casts = [
        'is_active'  => 'boolean',
    ];
}
