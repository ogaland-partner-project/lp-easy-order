<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TBasicKnowledge extends Model
{
    use SoftDeletes;

    protected $table = 't_basic_knowledges';
    protected $guarded = ['id'];

    /*
     * t_lp_ordersテーブルとのリレーション
     */
    public function lpOrder()
    {
        return $this->belongsTo(TLpOrder::class, 'lp_order_id');
    }

    /*
     * t_basic_knowledge_detailsテーブルとのリレーション
     */
    public function basicKnowledgeDetails()
    {
        return $this->hasMany(TBasicKnowledgeDetail::class, 'basic_knowledge_id');
    }

    /*
     * t_basic_knowledge_imagesテーブルとのリレーション
     */
    public function basicKnowledgeImages()
    {
        return $this->hasMany(TBasicKnowledgeImage::class, 'basic_knowledge_id');
    }

    /*
     * t_basic_knowledge_urlsテーブルとのリレーション
     */
    public function basicKnowledgeUrls()
    {
        return $this->hasMany(TBasicKnowledgeUrl::class, 'basic_knowledge_id');
    }
}
