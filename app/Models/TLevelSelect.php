<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TLevelSelect extends Model
{

    use SoftDeletes;

    protected $table = 't_level_selects';

    protected $guarded = [
        'id'
    ];

    public function tLpOrders() {
        return $this->belongsTo('App\Models\TLpOrder', 'id', 'lp_order_id');
    }

    public function tLevelSelectLpBlocks() {
        return $this->hasMany('App\Models\TLevelSelectLpBlock', 'level_select_id', 'id');
    }


    // 削除された際にリレーション先のTLevelSelectLpBlockのデータも削除する
    public static function boot()
    {
        parent::boot();
        static::deleting(function($header){
            $header->tLevelSelectLpBlocks()->delete();
        });
    }
}
