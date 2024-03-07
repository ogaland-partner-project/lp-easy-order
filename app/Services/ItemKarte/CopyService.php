<?php

namespace App\Services\ItemKarte;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Common\CommonMsg;
use App\Exceptions\AppException;
use App\Models\TLpOrder;
use App\Models\TItemKarte;
use App\Services\Common\CommonService;
use App\Services\ItemKarte\DeleteService;

class CopyService extends CommonService
{
    /**
     * 商品カルテ情報コピー
     *
     * @param array $param
     */
    public function execCopy($param)
    {
        try {

            DB::beginTransaction();

            // * コピー先LP構成情報
            $this->copyAbleCheckLpOrder($param['lp_order_id']);

            // * コピー元LP構成情報
            $this->copyAbleCheckLpOrder($param['other_lp_order_id']);

            // * 関連テーブルの活性確認
            $this->copyAbleCheckRelation($param);

            // * 既存データ削除
            $delService = new DeleteService;
            $delService->execDelete($param);

            // * データ複製
            $this->copyDataRelation($param);

            DB::commit();

        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    /**
     * ### 関連テーブルのコピー元データ活性確認
     *
     * @param array $param
     */
    private function copyAbleCheckRelation($param){

        $itemKartes = TItemKarte::where('lp_order_id', $param['other_lp_order_id']);

        if (!$itemKartes->exists()) {
            throw new AppException(CommonMsg::MSG_ID_000006);
        }
    }

    /**
     * コピー先へデータ複製処理
     * 
     * @param array $param
     */
    public function copyDataRelation($param){

        $otherLpOrder = TLpOrder::find($param['other_lp_order_id']);
        $otherLpOrder->itemKartes->each(function ($otherItemKarte) use ($param) {
            $itemKarte = $otherItemKarte->replicate();
            $itemKarte->lp_order_id = $param['lp_order_id'];
            $itemKarte->created_pg = 'ItemKarte.CopyService.execCopy';
            $itemKarte->updated_pg = 'ItemKarte.CopyService.execCopy';
            $itemKarte->save();
            $otherItemKarte->rawMaterials->each(function ($otherRawMaterial) use ($itemKarte) {
                $rawMaterial = $otherRawMaterial->replicate();
                $rawMaterial->item_karte_id = $itemKarte->id;
                $rawMaterial->created_pg = 'ItemKarte.CopyService.execCopy';
                $rawMaterial->updated_pg = 'ItemKarte.CopyService.execCopy';
                $rawMaterial->save();
            });
        });

    }
}
