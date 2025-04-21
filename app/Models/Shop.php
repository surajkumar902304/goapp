<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;
    protected $primaryKey = 'shop_id';
    protected $table = 'shops';

    protected $fillable = ['shop_id', 'shop_name', 'shop_status'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'shop_users', 'shop_id', 'user_id');
    }
    
    public function shop_users()
{
    return $this->hasMany(Shop_user::class);
}
}
