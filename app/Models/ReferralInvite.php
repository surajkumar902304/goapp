<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralInvite extends Model
{
    use HasFactory;
    protected $primaryKey = 'referral_invite_id';
    protected $table = 'referral_invites';
    protected $fillable = [
        'sender_user_id',
        'name',
        'city',
        'email',
        'referral_code'
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }
}
