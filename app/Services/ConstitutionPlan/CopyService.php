<?php

namespace App\Services\ConstitutionPlan;

use Illuminate\Support\Facades\DB;
use App\Models\TConstitutionPlan;
use Exception;
use App\Exceptions\AppException;
use App\Common\CommonMsg;
use App\Services\Common\CommonService;
use App\Services\ConstitutionPlan\DeleteService;

class CopyService extends CommonService
{

    public function __construct(){}

    /**
     * ## 構成案新規作成処理
     * 1. LP構成のデータ活性状態確認（コピー先）
     * 1. LP構成のデータ活性状態確認（コピー元）
     * 1. 関連テーブルの活性確認
     * 1. 既存データ削除
     *   - t_constitution_plans の既存データ削除
     *      - t_plan_imagesの既存データも連動して削除
     *      - t_plan_image_memos の既存データも連動して削除
     * 1. t_constitution_plans のコピー処理
     *      - t_plan_imagesの既存データも連動して削除
     *      - t_plan_image_memos の既存データも連動して削除
     *
     * @return void
     */
    public function exec($param)
    {
        try{
            DB::beginTransaction();

            // * LP構成のデータ活性状態確認（コピー先）
            $this->copyAbleCheckLpOrder($param['lp_order_id']);

            // * LP構成のデータ活性状態確認（コピー元）
            $this->copyAbleCheckLpOrder($param['other_lp_order_id']);

            // * 関連テーブルの活性確認
            $this->copyAbleCheckRelation($param);

            // * 既存データ削除
            $delService = new DeleteService;
            $delService->execDelete($param);

            // * データ複製
            $this->copyDataRelation($param);

            DB::commit();

        }catch(Exception $e){
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * ### 関連テーブルのコピー元データ活性確認
     *
     * @param array $param
     */
    public function copyAbleCheckRelation($param){

        $constitutionPlans = TConstitutionPlan::where('lp_order_id', $param['other_lp_order_id']);

        if (!$constitutionPlans->exists()) {
            throw new AppException(CommonMsg::MSG_ID_000006);
        }
    }

    /**
     * ## 構成案データのコピー処理
     *
     * 1. パラメータのother_lp_order_idに紐づく情報をコピー
     *
     * @param [type] $param
     * @return void
     */
    public function copyDataRelation($param){

        // コピー元データ取得
        $plans = TConstitutionPlan::where('lp_order_id', $param['other_lp_order_id']);

        // データを複製する＆一部項目は編集
        $plans->each(function($otherPlan)use($param){
            $plan = $otherPlan->replicate();
            $plan->lp_order_id = $param['lp_order_id'];
            $plan->created_pg = 'ConstitutionPlan.CopyService.copyDataRelation';
            $plan->updated_pg = 'ConstitutionPlan.CopyService.copyDataRelation';
            $plan->save();
            $otherPlan->planImage->each(function($otherPlanImage) use($plan){
                $planImage = $otherPlanImage->replicate();
                $planImage->constitution_plan_id = $plan->id;
                $planImage->created_pg = 'ConstitutionPlan.CopyService.copyDataRelation';
                $planImage->updated_pg = 'ConstitutionPlan.CopyService.copyDataRelation';
                $planImage->save();
            });
            $otherPlan->planImageMemo->each(function($otherPlanImageMemo) use($plan){
                $planImageMemo = $otherPlanImageMemo->replicate();
                $planImageMemo->constitution_plan_id = $plan->id;
                $planImageMemo->created_pg = 'ConstitutionPlan.CopyService.copyDataRelation';
                $planImageMemo->updated_pg = 'ConstitutionPlan.CopyService.copyDataRelation';
                $planImageMemo->save();
            });
        });
    }
}