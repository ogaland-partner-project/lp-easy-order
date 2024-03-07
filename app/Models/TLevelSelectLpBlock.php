<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TLevelSelectLpBlock extends Model
{

    use SoftDeletes;

    protected $table = 't_level_select_lp_blocks';

    protected $guarded = [
        'id'
    ];

    public function tLevelSelects() {
        return $this->belongsTo('App\Models\TLevelSelect', 'id', 'level_select_id');
    }

}
