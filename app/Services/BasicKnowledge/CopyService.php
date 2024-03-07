<?php

namespace App\Services\BasicKnowledge;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Common\CommonMsg;
use App\Exceptions\AppException;
use App\Models\TLpOrder;
use App\Services\Common\CommonService;
use App\Services\BasicKnowledge\DeleteService;

class CopyService extends CommonService
{
    /**
     * 基礎知識情報コピー
     *
     * @param array $param
     */
    public function execCopy($param)
    {
        try {
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

        $basicKnowledges = TBasicKnowledge::where('lp_order_id', $param['other_lp_order_id']);

        if (!$basicKnowledges->exists()) {
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

        $otherLpOrder->basicKnowledges->each(function ($otherBasicKnowledge) use ($param) {
            $basicKnowledge = $otherBasicKnowledge->replicate();
            $basicKnowledge->lp_order_id = $param['lp_order_id'];
            $basicKnowledge->created_pg = 'BasicKnowledge.CopyService.execCopy';
            $basicKnowledge->updated_pg = 'BasicKnowledge.CopyService.execCopy';
            $basicKnowledge->save();
            $otherBasicKnowledge->basicKnowledgeDetails->each(function ($otherDetail) use ($basicKnowledge) {
                $basicKnowledgeDetail = $otherDetail->replicate();
                $basicKnowledgeDetail->basic_knowledge_id = $basicKnowledge->id;
                $basicKnowledgeDetail->created_pg = 'BasicKnowledge.CopyService.execCopy';
                $basicKnowledgeDetail->updated_pg = 'BasicKnowledge.CopyService.execCopy';
                $basicKnowledgeDetail->save();
            });
            $otherBasicKnowledge->basicKnowledgeImages->each(function ($otherImage) use ($basicKnowledge) {
                $basicKnowledgeImage = $otherImage->replicate();
                $basicKnowledgeImage->basic_knowledge_id = $basicKnowledge->id;
                $basicKnowledgeImage->created_pg = 'BasicKnowledge.CopyService.execCopy';
                $basicKnowledgeImage->updated_pg = 'BasicKnowledge.CopyService.execCopy';
                $basicKnowledgeImage->save();
            });
            $otherBasicKnowledge->basicKnowledgeUrls->each(function ($otherUrl) use ($basicKnowledge) {
                $basicKnowledgeUrl = $otherUrl->replicate();
                $basicKnowledgeUrl->basic_knowledge_id = $basicKnowledge->id;
                $basicKnowledgeUrl->created_pg = 'BasicKnowledge.CopyService.execCopy';
                $basicKnowledgeUrl->updated_pg = 'BasicKnowledge.CopyService.execCopy';
                $basicKnowledgeUrl->save();
            });
        });
    }

}
