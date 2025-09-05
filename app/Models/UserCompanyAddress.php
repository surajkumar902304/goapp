<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCompanyAddress extends Model
{
    use HasFactory;
    protected $primaryKey = 'user_company_address_id';
    protected $table = 'user_company_addresses';
    protected $fillable = [
        'user_id', 
        'user_company_name', 
        'company_address1', 
        'company_address2', 
        'company_city', 
        'company_country', 
        'company_postcode'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getFullAddressAttribute()
    {
        $parts = [
            $this->user_company_name,
            $this->company_address1,
            $this->company_address2,
            $this->company_city,
            $this->company_country,
            $this->company_postcode,
        ];

        return collect($parts)
            ->filter()               
            ->implode(', ');       
    }
}
