<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mlocation extends Model
{
    use HasFactory;
    protected $primaryKey = 'mlocation_id';
    protected $table = 'mlocations';

    protected $fillable = ['name', 'adresss', 'is_default'];
}
