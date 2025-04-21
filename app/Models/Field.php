<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;

    protected $table = 'fields';
    protected $primaryKey = 'field_id';

    protected $fillable = [
        'field_name', 'product_field_name'
    ];

    public function queries()
    {
        return $this->belongsToMany(Query::class, 'field_query_relations', 'field_id', 'query_id');
    }
}
