<?php

namespace App\Services\FinalDesignConfirmation;

use App\Models\TFinalDesignConfirmation;

class DeleteService
{

    /**
     * ### データ削除処理
     *
     * @param array $param
     */
    public function execDelete($param){

        // データ削除処理
        TFinalDesignConfirmation::where('lp_order_id', $param['lp_order_id'])->delete();

    }

}