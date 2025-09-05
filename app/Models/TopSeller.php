<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopSeller extends Model
{
    use HasFactory;
    protected $primaryKey = 'top_seller_id';
    protected $table = 'top_sellers';
    protected $fillable = ['mvariant_id'];
    public function variant()
    {
        return $this->belongsTo(Mvariant::class, 'mvariant_id')->with('product');         
    }
}
