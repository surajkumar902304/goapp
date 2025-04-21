<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Moption extends Model
{
    use HasFactory;
    protected $primaryKey = 'moption_id';
    protected $fillable = ['moption_name'];
}
