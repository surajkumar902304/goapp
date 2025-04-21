<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Query extends Model
{
    use HasFactory;

    protected $table = 'queries';
    protected $primaryKey = 'query_id';

    protected $fillable = [
        'query_name',
    ];

    public function fields()
    {
        return $this->belongsToMany(Field::class, 'field_query_relations', 'query_id', 'field_id');
    }
}
