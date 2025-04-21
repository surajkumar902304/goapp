<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option_name extends Model
{
    use HasFactory;
    protected $primaryKey = 'option_id';
    protected $table = 'option_names';

    protected $fillable = ['option_id', 'option_name'];
}
