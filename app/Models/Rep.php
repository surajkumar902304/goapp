<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rep extends Model
{
    use HasFactory;
    protected $primaryKey = 'rep_id';
    protected $table = 'reps';

    protected $fillable = ['user_id', 'rep_code', 'commission_percent'];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
