<?php

namespace App\Services\LevelSelect;

use Illuminate\Support\Facades\DB;
use App\Models\TLevelSelect;
use App\Models\TLevelSelectLpBlock;
use App\Http\Requests\LevelSelectUpdateRequest;

/**
 * LP構成の更新
 * 機能ID: F-00001-00003
 */
class UpdateService
{

    /**
     * 更新
     *
     * @param int $lpOrderId
     * @param object $request
     * @return $rows
     */
    public function update($lpOrderId, LevelSelectUpdateRequest $request)
    {
        try {

            DB::beginTransaction();

            // t_level_selectsの更新対象データ取得⇒更新
            $row = TLevelSelect::where('lp_order_id', $lpOrderId)->first();
            $row->fill($request->all());
            $row->save();

            // パラメータのブロック情報
            $pBlocks = collect($request->t_level_select_lp_blocks);
            $pBlockIds = $pBlocks->pluck('id');

            // テーブルのブロック情報
            $levelSelectId = $request->id;
            $tBlockIds = TLevelSelectLpBlock::where('level_select_id', $levelSelectId)->get('id')->pluck('id');

            // パラメータと既存データのidの差分が削除対象
            $diffs = $tBlockIds->diff($pBlockIds)->toArray();
            collect($diffs)->each(function($diffId) {
                // 論理削除
                TLevelSelectLpBlock::find($diffId)->delete();
            });

            // upsert
            $pBlocks->each(function($pBlock, $index) use($levelSelectId) {
                TLevelSelectLpBlock::updateOrCreate(
                    [
                        "id" => $pBlock['id'],
                    ],
                    [
                        "level_select_id" => $levelSelectId,
                        "block_detail" => $pBlock['block_detail'],
                        "sort_order" => $index,
                    ]
                );
            });

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }
}
