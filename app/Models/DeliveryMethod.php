<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryMethod extends Model
{
    use HasFactory;
    protected $primaryKey = 'delivery_method_id';
    protected $table = 'delivery_methods';

    protected $fillable = ['delivery_method_name', 'delivery_method_amount', 'is_active'];

    protected $casts = [
        'is_active'  => 'boolean',
    ];
}
