<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;
    protected $primaryKey = 'wallet_id';
    protected $table = 'wallets';
    protected $fillable = ['user_id', 'balance'];

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class, 'wallet_id', 'wallet_id');
    }

}
