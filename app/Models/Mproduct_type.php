<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mproduct_type extends Model
{
    use HasFactory;
    protected $primaryKey = 'mproduct_type_id';
    protected $table = 'mproduct_types';

    protected $fillable = ['mproduct_type_name'];
}
