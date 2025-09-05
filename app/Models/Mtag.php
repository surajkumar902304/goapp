<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mtag extends Model
{
    use HasFactory;
    protected $primaryKey = 'mtag_id';
    protected $table = 'mtags';

    protected $fillable = ['mtag_name'];
}
