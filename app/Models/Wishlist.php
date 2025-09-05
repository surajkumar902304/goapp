<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;
    protected $primaryKey = 'wishlist_id';
    protected $table = 'wishlists';

    protected $fillable = ['user_id', 'mvariant_id'];

    public function product()
    {
        return $this->belongsTo(Mvariant::class, 'mvariant_id');
    }
}
