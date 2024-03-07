<?php

namespace App\Services\LevelSelect;

use App\Models\TLevelSelect;
use App\Http\Resources\LevelSelectResource;

/**
 * レベル別質問事項画面の各項目を検索
 * 機能ID: F-00002-00001
 */
class ShowService
{

    /**
     * 検索
     *
     * @param int $lpOrderId
     * @return $rows
     */
    public function findById($lpOrderId)
    {
        // 1lpOrderIdに対して1TLevelSelectなのでfirst()
        $row = TLevelSelect::where('lp_order_id', $lpOrderId)->first();
        if(is_null($row)) {
            return [];
        }
        $block = $row->tLevelSelectLpBlocks()->orderBy('sort_order')->get();
        if(!empty($block)) $row->tLevelSelectLpBlocks = $block;
        return new LevelSelectResource($row);
    }
}
