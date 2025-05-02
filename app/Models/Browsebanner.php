<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Browsebanner extends Model
{
    use HasFactory;
    protected $primaryKey = 'browsebanner_id';
    protected $table = 'browsebanners';

    protected $fillable = ['mcat_id', 'browsebanner_name','browsebanner_image','browsebanner_position'];
}
