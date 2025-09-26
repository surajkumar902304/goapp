<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTag extends Model
{
    use HasFactory;
    protected $primaryKey = 'user_tag_id';
    protected $table = 'user_tags';
    protected $fillable = ['user_tag_name', 'type', 'discount', 'is_active'];

    protected $casts = [
        'is_active'  => 'boolean',
    ];

    public function userTag()
    {
        return $this->hasMany(UserTagPrice::class, 'user_tag_id', 'user_tag_id');
    }
}
