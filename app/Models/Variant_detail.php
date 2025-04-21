<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variant_detail extends Model
{
    use HasFactory;
    protected $primaryKey = 'variant_detail_id';
    protected $table = 'variant_details';

    protected $fillable = ['option_ids', 'options', 'variant_id'];

    protected $casts = [
        'option_ids'=> 'array',
        'options'=> 'array',

    ];

    public function variant()
    {
        return $this->belongsTo(Variant::class, 'variant_id', 'variant_id');
    }
}
