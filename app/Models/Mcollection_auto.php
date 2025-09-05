<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mcollection_auto extends Model
{
    use HasFactory;
    protected $table = 'mcollection_autos';
    protected $primaryKey = 'collection_auto_id';

    protected $fillable = [
        'msubcat_id', 'field_id', 'query_id', 'value', 'logical_operator'
    ];

    public function queryRelation()
    {
        return $this->belongsTo(Query::class, 'query_id', 'query_id');
    }

    public function field()
    {
        return $this->belongsTo(Field::class, 'field_id', 'field_id');
    }
}
