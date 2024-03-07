<?php

namespace App\Services\ItemKarte;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Common\CommonMsg;
use App\Models\TLpOrder;
use App\Models\TItemKarte;
use App\Models\TRawMaterial;

class CreateService
{
    /**
     * 商品カルテ情報登録
     *
     * @param array $param
     */
    public function execCreate($param)
    {
        try {
            DB::beginTransaction();

            // t_lp_ordersテーブルにlp_order_idが存在しない場合はエラー
            if (!TLpOrder::find($param['lp_order_id'])) {
                throw new AppException(CommonMsg::MSG_ID_000805 . $param['lp_order_id']);
            }

            $itemKarte = new TItemKarte();
            $row = $param;
            unset($row['material_list']);
            $row = array_merge($row, [
                'created_pg' => 'ItemKarte.CreateService.execCreate',
                'updated_pg' => 'ItemKarte.CreateService.execCreate',
            ]);
            $itemKarte->fill($row)->save();

            collect($param['material_list'])->each(function ($row) use ($itemKarte) {
                $rawMaterial = new TRawMaterial();
                $row = array_merge($row, [
                    'item_karte_id' => $itemKarte->id,
                    'created_pg' => 'ItemKarte.CreateService.execCreate',
                    'updated_pg' => 'ItemKarte.CreateService.execCreate',
                ]);
                $rawMaterial->fill($row)->save();
            });

            DB::commit();
            return $itemKarte->id;
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }
}
