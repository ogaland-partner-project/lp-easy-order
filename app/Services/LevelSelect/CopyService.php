<?php

namespace App\Services\LevelSelect;

use App\Models\TLevelSelect;
use App\Services\Common\CommonService;

class CopyService extends CommonService
{

    /**
     * コピー先へデータ複製処理
     * 
     * レベル別質問事項は、独自のコピー機能を持ちません。
     * 当該メソッドがコールされるのは、HomeAPIでコピーメソッドが呼ばれた際に限ります。
     * したがって、他APIのコピーメソッドが持つ「関連テーブルの活性確認」および「既存データ削除」のメソッドは持ちません
     * 
     * @param array $param
     */
    public function copyDataRelation($param){

        // コピー元データ取得
        $origenLevelSelects = TLevelSelect::where('lp_order_id', $param['other_lp_order_id'])->get();

        // データを複製する＆一部項目は編集
        $origenLevelSelects->each(function ($clonedLevelSelect) use ($param) {
            $origenLevelSelect = $clonedLevelSelect->replicate();
            $origenLevelSelect->lp_order_id = $param['lp_order_id'];
            $origenLevelSelect->created_pg = 'LevelSelect.CopyService.PlanCopy';
            $origenLevelSelect->updated_pg = 'LevelSelect.CopyService.PlanCopy';
            $origenLevelSelect->save();
            $clonedLevelSelect->tLevelSelectLpBlocks()->each(function ($clonedlevelSelectLpBlock) use ($origenLevelSelect) {
                $lp_block = $clonedlevelSelectLpBlock->replicate();
                $lp_block->level_select_id = $origenLevelSelect->id;
                $lp_block->created_pg = 'LevelSelect.CopyService.PlanCopy';
                $lp_block->updated_pg = 'LevelSelect.CopyService.PlanCopy';
                $lp_block->save();
            });
        });
    }
}