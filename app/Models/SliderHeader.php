<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SliderHeader extends Model
{
    use HasFactory;
    protected $table = 'slider_headers';
    protected $primaryKey = 'slider_header_id';
    protected $fillable = ['header_name', 'header_value'];

}
