<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TComparisonInsertItem extends Model
{
    protected $table = 't_comparison_insert_items';
    //
    use SoftDeletes;
    protected $fillable  = [
        'id',
        'comparison_header_id',
        'text',
        'color',
        'col',
        'created_pg',
        'created_at',
        'updated_pg',
        'updated_at',
        'deleted_pg',
        'deleted_at',
    ];


    public function comparisonInsertHeader()
    {
        return $this->belongsTo(TComparisonInsertHeader::class, 'comparison_header_id');
    }
}
