<?php

namespace App\Services\Home;

use Illuminate\Support\Facades\DB;
use App\Models\TLpOrder;
use App\Services\BasicKnowledge\DeleteService as BasicKnowledgeDeleteService;
use App\Services\ComparisonInsert\DeleteService as ComparisonInsertDeleteService;
use App\Services\ConstitutionPlan\DeleteService as ConstitutionPlanDeleteService;
use App\Services\ConstitutionProcess\DeleteService as ConstitutionProcessDeleteService;
use App\Services\FinalDesignConfirmation\DeleteService as FinalDesignConfirmationDeleteService;
use App\Services\ItemKarte\DeleteService as ItemKarteDeleteService;
use App\Services\LevelSelect\DeleteService as LevelSelectDeleteService;


/**
 * LP構成の削除
 * 機能ID: F-00001-00004
 */
class DeleteService
{

    /**
     * データの削除
     * * 親子関係にあるテーブル全てのデータを論理削除する。
     *   t_lp_ordersは最後に削除する。
     *
     * @param int $lpOrderId
     */
    public function execDelete($lpOrderId)
    {

        try {

            DB::beginTransaction();

            // 親子孫関係にあるテーブルのデータを削除

            $param['lp_order_id'] = $lpOrderId;

            // レベル別質問事項
            $levelSelectDeleteService = new LevelSelectDeleteService();
            $levelSelectDeleteService->execDelete($param);

            // 商品カルテ入力
            $itemKarteDeleteService = new ItemKarteDeleteService();
            $itemKarteDeleteService->execDelete($param);

            // 基礎知識
            $basicKnowledgeDeleteService = new BasicKnowledgeDeleteService();
            $basicKnowledgeDeleteService->execDelete($param);

            // 他社比較入力
            $comparisonInserDeleteService = new ComparisonInsertDeleteService();
            $comparisonInserDeleteService->execDelete($param);

            // 構成の手順
            $constitutionProcessDeleteService = new ConstitutionProcessDeleteService();
            $constitutionProcessDeleteService->execDelete($param);

            // 構成案
            $constitutionPlanDeleteService = new ConstitutionPlanDeleteService();
            $constitutionPlanDeleteService->execDelete($param);

            // 最終デザイン確認
            $finalDesignConfirmationDeleteService  = new FinalDesignConfirmationDeleteService();
            $finalDesignConfirmationDeleteService->execDelete($param);

            // 対象データの削除
            // TLpOrderの削除は最後に実施
            TLpOrder::where("id", $lpOrderId)->delete();

            DB::commit();

        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }

    }

}
