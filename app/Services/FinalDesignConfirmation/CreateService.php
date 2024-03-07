<?php

namespace App\Services\FinalDesignConfirmation;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Common\CommonMsg;
use App\Services\Common\CommonService;
use App\Exceptions\AppException;
use App\Models\TLpOrder;
use App\Models\TFinalDesignConfirmation;
class CreateService extends CommonService
{
    /**
     * ### 最終デザイン確認新規登録
     * 1. t_final_design_confirmations への登録
     * 1. t_final_design_confirmations への登録,画像の保存処理
     * @param array $param
     */
    public function execCreate($param)
    {
        try {
            DB::beginTransaction();

            // リクエストの値を登録データとして設定
            collect($param['design_parts'])->each(function ($item ,$index) {

                // t_lp_ordersテーブルにlp_order_idが存在しない場合はエラー
                if (!TLpOrder::find($item['lp_order_id'])) {
                    throw new AppException(CommonMsg::MSG_ID_000006);
                }

                // 画像パス、画像ファイルを除く情報を設定して保存
                $finalDesignConfirmation = new TFinalDesignConfirmation();
                $finalDesignConfirmation->fill($item);
                $finalDesignConfirmation->sort_order = $index;
                $finalDesignConfirmation->created_pg = 'FinalDesignConfirmation.CreateService.execCreate';
                $finalDesignConfirmation->updated_pg = 'FinalDesignConfirmation.CreateService.execCreate';
                $finalDesignConfirmation->save();

                $callingApiName = 'finaldesignconfirmation';

                // 画像ファイルのアップロードと、画像情報の保存
                $fileImageData = $item['file'];

                if ($this->existsValue($fileImageData)){
                    $this->saveImageFiletFromParamString(
                        $finalDesignConfirmation,
                        $item->lpOrderId,
                        'file',
                        $callingApiName,
                        'image_path',
                        null );
                }

            });

            DB::commit();

        }catch(Exception $e){
            DB::rollBack();
            throw $e;
        }
    }
}
