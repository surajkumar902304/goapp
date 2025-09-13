<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;
    protected $primaryKey = 'ref_id';
    protected $table = 'referrals';
    protected $fillable = [
        'user_id',
        'referrer_id',
        'has_received_bonus'
    ];

    protected $casts = [
        'has_received_bonus'  => 'boolean',
    ];

}
