<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TConstitutionBlock extends Model
{
    protected $table = 't_constitution_blocks';
    //
    use SoftDeletes;

    protected $fillable = [
        'id',
        'constitution_process_id',
        'block_detail',
        'sort_order',
        'created_pg',
        'created_at',
        'updated_pg',
        'updated_at',
        'deleted_pg',
        'deleted_at',
    ];

    public function tConstitutionProcesses() {
        return $this->belongsTo(TConstitutionProcess::class, 'constitution_process_id');
    }

}
