<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TPlanThumbnailImage extends Model
{
    use SoftDeletes;

    protected $table = 't_plan_thumbnail_images';

    protected $fillable = [
        'plan_thumbnail_id',
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

    // リレーション: サムネイルに属する
    public function planThumbnail()
    {
        return $this->belongsTo(TPlanThumbnail::class, 'plan_thumbnail_id');
    }
}