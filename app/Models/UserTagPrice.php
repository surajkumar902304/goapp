<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTagPrice extends Model
{
    use HasFactory;
    protected $primaryKey = 'user_tag_price_id';
    protected $table = 'user_tag_prices';
    protected $fillable = ['user_tag_id', 'mvariant_id', 'tag_price'];
}
