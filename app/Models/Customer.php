<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use Notifiable;
    use HasFactory;
    protected $primaryKey = 'rep_id';
    protected $table = 'customers';
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'rep_code',
        'commission_percent'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'rep_id');
    }

}
