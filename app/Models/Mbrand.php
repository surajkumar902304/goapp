<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mbrand extends Model
{
    use HasFactory;
    protected $primaryKey = 'mbrand_id';
    protected $table = 'mbrands';

    protected $fillable = ['mbrand_name'];

    public function mproducts()
    {
        return $this->hasMany(Mproduct::class, 'mbrand_id', 'mbrand_id');
    }
}
