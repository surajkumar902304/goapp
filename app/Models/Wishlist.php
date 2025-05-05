<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;
    protected $primaryKey = 'wishlist_id';
    protected $table = 'wishlists';

    protected $fillable = ['user_id', 'mproduct_id'];

    public function product()
    {
        return $this->belongsTo(Mproduct::class, 'mproduct_id');
    }
}
