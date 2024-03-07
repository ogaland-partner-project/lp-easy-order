<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TConstitutionProcess extends Model
{
    protected $table = 't_constitution_processes';
    //
    use SoftDeletes;

    protected $fillable = [
        'id',
        'lp_order_id',
        'concept_word',
        'concept_catch',
        'how_block',
        'created_pg',
        'created_at',
        'updated_pg',
        'updated_at',
        'deleted_pg',
        'deleted_at',
    ];

    public function tLpOrders() {
        return $this->belongsTo(TLpOrder::class, 'lp_order_id');
    }

    public function tConstitutionCatchphrases() {
        return $this->hasMany(TConstitutionCatchphrase::class, 'constitution_process_id');
    }

    public function tConstitutionHowBlocks() {
        return $this->hasMany(TConstitutionHowBlock::class, 'constitution_process_id');
    }

    public function tConstitutionBlocks() {
        return $this->hasMany(TConstitutionBlock::class, 'constitution_process_id');
    }

    public function tConstitutionFixBlocks() {
        return $this->hasMany(TConstitutionFixBlock::class, 'constitution_process_id');
    }

    // 削除された際にリレーション先のデータも削除する
    public static function boot()
    {
        parent::boot();
        static::deleting(function($process){
            $process->tConstitutionCatchphrases()->delete();
            $process->tConstitutionHowBlocks()->delete();
            $process->tConstitutionBlocks()->delete();
            $process->tConstitutionFixBlocks()->delete();
        });
    }


}
