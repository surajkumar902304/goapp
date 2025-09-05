<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyRewardBanner extends Model
{
    use HasFactory;
    protected $primaryKey = 'loyalty_reward_banner_id';
    protected $table = 'loyalty_reward_banners';

    protected $fillable = ['loyalty_reward_banner_image'];

}
