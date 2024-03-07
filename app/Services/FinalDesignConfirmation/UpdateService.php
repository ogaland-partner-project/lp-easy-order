<?php

namespace App\Services\FinalDesignConfirmation;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Common\CommonMsg;
use App\Services\Common\CommonService;
use App\Exceptions\AppException;
use App\Models\TLpOrder;
use App\Models\TFinalDesignConfirmation;

class UpdateService extends CommonService
{
    /**
     * ### 最終デザイン確認情報更新
     *
     * * t_final_design_confirmationsテーブルの更新を行う。
     *      * $paramの状態によって
     *
     * @param int $lpOrderId
     * @param array $param
     */
    public function execUpdate($lpOrderId, $param)
    {

        try {
            DB::beginTransaction();

            // LP構成のデータ活性状態確認
            $lpOrder = TLpOrder::find($lpOrderId);
            if (!$lpOrder) {
                throw new AppException(CommonMsg::MSG_ID_000006 . "ID:" . $lpOrderId);
            }

            // 更新対象の最終デザイン確認データ存在確認と取得
            $finalDesignConfirmations = TFinalDesignConfirmation::where('lp_order_id', $lpOrderId);

            // 削除処理
            $this->execRowDelete($param,$finalDesignConfirmations);

            // 更新・登録処理
            $this->execRowUpdateCreate($lpOrderId, $param);

            DB::commit();

        }catch(Exception $e){
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 最終デザイン確認情報更新(削除)関数
     *
     * @param array $param
     * @param object $finalDesignConfirmations
     */
    private function execRowDelete($param, $finalDesignConfirmations){

        // * $paramに存在せず、t_final_design_confirmationsにのみ存在する：削除処理
        $ids = collect($param['design_parts'])->filter(function ($row) {
            return isset($row['id']);
        })->map(function ($row) {
            return $row['id'];
        });

        $finalDesignConfirmations->whereNotIn('id',$ids)->delete();
    }

    /**
     * 最終デザイン確認情報更新(更新・登録)関数
     *
     * @param int $lpOrderId
     * @param array $param
     */
    private function execRowUpdateCreate($lpOrderId, $param){
        collect($param['design_parts'])->each(function ($row, $index) use ($lpOrderId) {

            $finalDesignConfirmation = new TFinalDesignConfirmation();

            // design_parts.idの状態で処理分岐
            //  * $paramとt_final_design_confirmationsの両方に存在する：更新処理
            //  * $paramにのみ存在する：登録処理
            $designPartsId = $row['id'];

            if ($this->existsValue($designPartsId)) {
                // 更新ルート
                $finalDesignConfirmation = TFinalDesignConfirmation::find($designPartsId);
                if (!$finalDesignConfirmation) {
                    throw new AppException(CommonMsg::MSG_ID_000006 . "ID:" . $designPartsId);
                }
            } else {
                // 登録ルート
                $finalDesignConfirmation->lp_order_id = $lpOrderId;
                $finalDesignConfirmation->created_pg = 'FinalDesignConfirmation.UpdateService.execUpdate';
            }

            $finalDesignConfirmation->fill($row);

            $finalDesignConfirmation->sort_order = $index;
            $finalDesignConfirmation->updated_pg = 'FinalDesignConfirmation.UpdateService.execUpdate';
            $finalDesignConfirmation->save();

            $callingApiName = 'finaldesignconfirmation';

            // 画像ファイルのアップロードと、画像情報の保存
            $fileImageData = $row['file'];

            if ($this->existsValue($fileImageData))  {
                $this->saveImageFiletFromParamString(
                    $finalDesignConfirmation,
                    $lpOrderId,
                    $fileImageData,
                    $callingApiName,
                    'image_path',
                    null );
            }
        });
    }
}