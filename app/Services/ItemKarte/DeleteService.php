<?php

namespace App\Services\ItemKarte;

use App\Models\TLpOrder;

class DeleteService
{
    /**
     * ### データ削除処理
     *
     * @param array $param
     */
    public function execDelete($param){

        $lpOrder = TLpOrder::find($param['lp_order_id']);

        $lpOrder->itemKartes->each(function ($itemKarte) {
            $itemKarte->rawMaterials->each(function ($rawMaterials) {
                $rawMaterials->delete();
            });
            $itemKarte->delete();
        });
    }
}
