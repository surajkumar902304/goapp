<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceSolution extends Model
{
    use HasFactory;
    protected $primaryKey = 'service_solution_id';
    protected $table = 'service_solutions';
    protected $fillable = [
        'service_solution_title', 
        'service_solution_sub_title', 
        'service_solution_image',
        'service_solution_desc'
    ];
}
