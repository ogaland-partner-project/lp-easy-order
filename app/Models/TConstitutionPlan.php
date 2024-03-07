<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TConstitutionPlan extends Model
{
    protected $table = 't_constitution_plans';
    //
    use SoftDeletes;

    protected $fillable  = [
        "id",
        "lp_order_id",
        "block_detail",
        "requester_fix",
        "pharmaceutical_affairs_fix",
        "information_management_memo",
        "sort_order",
        "created_pg",
        "created_at",
        "updated_pg",
        "updated_at",
        "deleted_pg",
        "deleted_at",
    ];

    public function lpOrder()
    {
        return $this->belongsTo(TLpOrder::class, 'lp_order_id');
    }

    public function planImage()
    {
        return $this->hasMany(TPlanImage::class, 'constitution_plan_id');
    }

    public function planImageMemo()
    {
        return $this->hasMany(TPlanImageMemo::class, 'constitution_plan_id');
    }

    // 削除された際にリレーション先のTComparisonInsertItemのデータも削除する
    public static function boot()
    {
        parent::boot();
        static::deleting(function($plan){
            $plan->planImage()->delete();
            $plan->planImageMemo()->delete();
        });
    }
}
