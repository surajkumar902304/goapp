<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mvariant_detail extends Model
{
    use HasFactory;
    protected $primaryKey = 'mvariant_detail_id';
    protected $table = 'mvariant_details';

    protected $fillable = ['mvariant_id', 'options', 'option_value'];

    protected $casts = [
        'options'=> 'array',
        'option_value'=> 'array',
    ];
}
