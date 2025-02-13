<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TPlanThumbnailImageMemo extends Model
{
    use SoftDeletes;

    protected $table = 't_plan_thumbnail_image_memos';

    protected $fillable = [
        'plan_thumbnail_id',
        'memo',
        'memo_category',
        'sort_order',
        'created_pg',
        'updated_pg',
        'deleted_pg'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // リレーション: サムネイルに属する
    public function planThumbnail()
    {
        return $this->belongsTo(TPlanThumbnail::class, 'plan_thumbnail_id');
    }
}