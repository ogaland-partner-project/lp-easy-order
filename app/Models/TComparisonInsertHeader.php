<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TComparisonInsertHeader extends Model
{
    protected $table = 't_comparison_insert_headers';
    //
    use SoftDeletes;

    protected $fillable  = [
        'id',
        'lp_order_id',
        'header_name',
        'header_type',
        'calculation_type',
        'calculation_row',
        'comparison_insert_flag',
        'companies_comparison_flag',
        'sort_order',
        'created_pg',
        'created_at',
        'updated_pg',
        'updated_at',
        'deleted_pg',
        'deleted_at'
    ];

    public function comparisonInsertItem()
    {
        return $this->hasMany(TComparisonInsertItem::class, 'comparison_header_id');
    }

    // 削除された際にリレーション先のTComparisonInsertItemのデータも削除する
    public static function boot()
    {
        parent::boot();
        static::deleting(function($header){
            $header->comparisonInsertItem()->delete();
        });
    }
}
