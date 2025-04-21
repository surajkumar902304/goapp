<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mstock extends Model
{
    use HasFactory;
    protected $primaryKey = 'mstock_id';
    protected $table = 'mstocks';

    protected $fillable = ['quantity', 'mlocation_id', 'mvariant_id'];

    public function mlocation()
    {
        return $this->belongsTo(Mlocation::class, 'mlocation_id', 'mlocation_id')
        ->select(['mlocation_id', 'name', 'adresss', 'is_default']);
    }
}
