<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TBasicKnowledgeDetail extends Model
{
    use SoftDeletes;

    protected $table = 't_basic_knowledge_details';
    protected $guarded = ['id'];

    /*
     * t_basic_knowledgesテーブルとのリレーション
     */
    public function basicKnowledge()
    {
        return $this->belongsTo(TBasicKnowledge::class, 'basic_knowledge_id');
    }
}
