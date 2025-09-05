<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart_item extends Model
{
    use HasFactory;
    protected $primaryKey = 'cart_item_id';
    protected $table = 'cart_items';

    protected $fillable = ['user_id', 'mvariant_id', 'quantity','status'];

    public function mvariant()
    {
        return $this->belongsTo(Mvariant::class, 'mvariant_id', 'mvariant_id');
    }

}
