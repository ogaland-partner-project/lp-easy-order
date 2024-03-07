<?php

namespace App\Services\Home;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Exceptions\AppException;
use App\Models\TLpOrder;
use App\Http\Requests\LpOrderCopyRequest;
use App\Services\LevelSelect\CopyService as LevelSelectCopyService;
use App\Services\ItemKarte\CopyService as ItemKarteCopyService;
use App\Services\BasicKnowledge\CopyService as BasicKnowledgeCopyService;
use App\Services\ComparisonInsert\CopyService as ComparisonInsertCopyService;
use App\Services\ConstitutionProcess\CopyService as ConstitutionProcessCopyService;
use App\Services\ConstitutionPlan\CopyService as ConstitutionPlanCopyService;
use App\Services\FinalDesignConfirmation\CopyService as FinalDesignConfirmationCopyService;
use App\Services\Common\CommonService;
use Carbon\Carbon;

/**
 * LP構成の複製
 * 機能ID: F-00001-00005
 */
class CopyService extends CommonService
{

    /**
     * ### LP構成の複製
     *
     * @param object $request
     */
    public function execCopy($request)
    {

        try {

            DB::beginTransaction();

            // * 活性確認（t_lp_orders）
            // LP構成のデータ活性状態確認（コピー元）
            $otherLpOrder = TLpOrder::find($request['other_lp_order_id']);
            $this->copyAbleCheckLpOrder($otherLpOrder);

            // * データ複製して新規登録（t_lp_orders）
            $copiedLpOrderId = $this->copyDataForLpOder($otherLpOrder);

            // * t_lp_ordersの新しいidをリクエストパラメータに追加
            $request['lp_order_id'] = $copiedLpOrderId;

            // * データ複製（関連テーブル）
            $this->copyDataFromHome($request);

            DB::commit();
        } catch (Exception $ex) {
            DB::rollback();
            throw $ex;
        }

    }

    /**
     * コピー先へデータ複製処理（t_lp_orders）
     *
     * @param collection $otherLpOrder
     * @return int $copiedLpOrderId
     */
    private function copyDataForLpOder($otherLpOrder){

        // LP構成名の接頭に「コピー - 」を追加
        $copiedProductName = 'コピー - ' . $otherLpOrder['product_name'];

        // LP構成の複製
        $lpOrder = new TLpOrder();
        $otherLpOrderToArray = $otherLpOrder->toArray();
        $lpOrder->fill($otherLpOrderToArray);
        $lpOrder['product_name'] = $copiedProductName;
        $lpOrder['editing'] = 0;
        $lpOrder['status'] = 0;
        $lpOrder->save();

        // コピーされたLP構成のID取得
        $copiedLpOrderId = $lpOrder->id;

        return $copiedLpOrderId;
    }

    /**
     * コピー先へデータ複製処理（関連テーブル）
     *
     * @param array $param
     */
    private function copyDataFromHome($param){

        // レベル別質問事項
        $levelSelectCopyService = new LevelSelectCopyService();
        $levelSelectCopyService->copyDataRelation($param);

        // 商品カルテ入力
        $itemKarteCopyService = new ItemKarteCopyService();
        $itemKarteCopyService->copyDataRelation($param);

        // 基礎知識
        $basicKnowledgeCopyService = new BasicKnowledgeCopyService();
        $basicKnowledgeCopyService->copyDataRelation($param);

        // 他社比較入力
        $comparisonInserCopyService = new ComparisonInsertCopyService();
        $comparisonInserCopyService->copyDataRelation($param);

        // 構成の手順
        $constitutionProcessCopyService = new ConstitutionProcessCopyService();
        $constitutionProcessCopyService->copyDataRelation($param);

        // 構成案
        $constitutionPlanCopyService = new ConstitutionPlanCopyService();
        $constitutionPlanCopyService->copyDataRelation($param);

        // 最終デザイン確認
        $finalDesignConfirmationCopyService  = new FinalDesignConfirmationCopyService();
        $finalDesignConfirmationCopyService->copyDataRelation($param);
    }

}
