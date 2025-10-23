<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StripeIntegration extends Model
{
    use HasFactory;
    protected $primaryKey = 'stripe_integration_id';
    protected $table = 'stripe_integrations';

    protected $fillable = [
        'provider', 
        'publishable_key', 
        'secret_key',
        'webhook_secret',
        'test_mode',
        'note',
        'is_active'
    ];

    protected $casts = [
        'test_mode'  => 'boolean',
        'is_active'  => 'boolean',
    ];
}
