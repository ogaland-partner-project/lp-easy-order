<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TRawMaterial extends Model
{
    use SoftDeletes;

    protected $table = 't_raw_materials';
    protected $guarded = ['id'];

    /*
     * t_item_kartesテーブルとのリレーション
     */
    public function itemKarte()
    {
        return $this->belongsTo(TItemKarte::class, 'item_karte_id');
    }
}
