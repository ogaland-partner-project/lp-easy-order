<?php

namespace App\Services\FinalDesignConfirmation;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Common\CommonMsg;
use App\Exceptions\AppException;
use App\Models\TLpOrder;
use App\Models\TFinalDesignConfirmation;
use App\Services\Common\CommonService;
use App\Services\FinalDesignConfirmation\DeleteService;

class CopyService extends CommonService
{

    /**
     * ### 最終デザイン確認コピー
     * 1. t_final_design_confirmationsのコピー処理
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
    private function copyAbleCheckRelation($param){
        $finalDesignConfirmations = TFinalDesignConfirmation::where('lp_order_id', $param['other_lp_order_id']);
        if (!$finalDesignConfirmations->exists()) {
            throw new AppException(CommonMsg::MSG_ID_000006);
        }
    }

    /**
     * コピー先へデータ複製処理
     * 
     * @param array $param
     */
    public function copyDataRelation($param){

        // コピー元最終デザイン確認データ取得
        $origenData = TFinalDesignConfirmation::where('lp_order_id', $param['other_lp_order_id'])->get();

        // データを複製する＆一部項目は編集
        $clonedData = $origenData->map(function ($item) use ($param){

            // 新しい値を設定する
            unset($item->id);
            $lpOderId = $param['lp_order_id'];
            $item->lp_order_id = $lpOderId;
            $item->created_pg = 'FinalDesignConfirmation.CopyService.execCopy';
            $item->created_at = now();

            return $item;
        });

        // 複製したデータを挿入する
        TFinalDesignConfirmation::insert($clonedData->toArray());

    }
}