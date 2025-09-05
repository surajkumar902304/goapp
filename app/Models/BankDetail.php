<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankDetail extends Model
{
    use HasFactory;
    protected $primaryKey = 'bank_detail_id';
    protected $table = 'bank_details';

    protected $fillable = [
        'company_name', 
        'bank_name', 
        'account_number',
        'sort_code',
        'note',
        'is_active'
    ];

    protected $casts = [
        'is_active'  => 'boolean',
    ];

}
