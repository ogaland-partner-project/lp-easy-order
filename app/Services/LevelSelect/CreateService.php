<?php

namespace App\Services\LevelSelect;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Exceptions\AppException;
use App\Models\TLevelSelect;
use App\Models\TLevelSelectLpBlock;
use App\Http\Requests\LevelSelectCreateRequest;

/**
 * レベル別質問事項画面の各項目を登録
 * 機能ID: F-00002-00004
 */
class CreateService
{

    /**
     * 新規登録
     *
     * @param object $request
     */
    public function insert(LevelSelectCreateRequest $request)
    {

        try {
            DB::beginTransaction();

            // レベル別質問項目の登録
            $row = new TLevelSelect();
            $row->fill($request->all());
            $row->save();

            $id = $row['id'];

            // ブロックデータを登録（レベルⅡ選択時のみ機能）
            $blocks = collect($request->t_level_select_lp_blocks);
            $blocks->each(function($block, $index) use($id) {
                $row_bl = new TLevelSelectLpBlock();
                $row_bl->level_select_id = $id;
                $row_bl->block_detail = $block['block_detail'];
                $row_bl->sort_order = $index;
                $row_bl->save();
            });

            DB::commit();
        } catch (Exception $ex) {
            DB::rollback();
            throw $ex;
        }

    }
}
