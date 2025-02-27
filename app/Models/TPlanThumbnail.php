<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TPlanThumbnail extends Model
{
    use SoftDeletes;

    protected $table = 't_plan_thumbnails';

    protected $fillable = [
        'lp_order_id',
        'block_detail',
        'requester_fix',
        'pharmaceutical_affairs_fix',
        'information_management_memo',
        'image_path',
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

    // リレーション: LPオーダーに属する
    public function lpOrder()
    {
        return $this->belongsTo(TLpOrder::class, 'lp_order_id');
    }

    // リレーション: 複数の画像を持つ
    public function images()
    {
        return $this->hasMany(TPlanThumbnailImage::class, 'plan_thumbnail_id');
    }

    // リレーション: 複数のメモを持つ
    public function memos()
    {
        return $this->hasMany(TPlanThumbnailImageMemo::class, 'plan_thumbnail_id');
    }
}