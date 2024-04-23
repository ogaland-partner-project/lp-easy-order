<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TLpOrder extends Model
{
    use SoftDeletes;

    protected $table = 't_lp_orders';

    protected $guarded = [
        'id'
    ];

    /*
     * t_item_kartesテーブルとのリレーション
     */
    public function itemKartes()
    {
        return $this->hasMany(TItemKarte::class, 'lp_order_id');
    }

    /**
     * t_basic_knowledgesテーブルとのリレーション
     */
    public function basicKnowledges()
    {
        return $this->hasMany(TBasicKnowledge::class, 'lp_order_id');
    }

    /**
     * t_level_selectsテーブルとのリレーション
     */
    public function tLevelSelects() {
        return $this->hasOne('App\Models\TLevelSelect', 'lp_order_id', 'id');
    }

    /**
     * t_constitution_processesテーブルとのリレーション
     */
    public function tConstitutionProcesses() {
        return $this->hasMany('App\Models\TConstitutionProcess', 'lp_order_id', 'id');
    }

    /**
     * t_lp_order_locksテーブルとのリレーション
     */
    public function tLpOrderLocks() {
        return $this->hasMany('App\Models\TLpOrderLock', 'lp_order_id', 'id');
    }

}
