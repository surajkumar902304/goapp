<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntegrationSetting extends Model
{
    use HasFactory;
    protected $primaryKey = 'integration_setting_id';
    protected $table = 'integration_settings';

    protected $fillable = [
        'provider', 
        'public_key', 
        'secret_key',
        'is_active'
    ];

    protected $casts = [
        'is_active'  => 'boolean',
    ];
}
