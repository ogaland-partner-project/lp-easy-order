<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TItemKarte extends Model
{
    use SoftDeletes;

    protected $table = 't_item_kartes';
    protected $guarded = ['id'];

    /*
     * t_lp_ordersテーブルとのリレーション
     */
    public function lpOrder()
    {
        return $this->belongsTo(TLpOrder::class, 'lp_order_id');
    }

    /*
     * t_raw_materialsテーブルとのリレーション
     */
    public function rawMaterials()
    {
        return $this->hasMany(TRawMaterial::class, 'item_karte_id');
    }
}
