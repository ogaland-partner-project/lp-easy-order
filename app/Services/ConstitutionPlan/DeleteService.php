<?php

namespace App\Services\ConstitutionPlan;

use App\Models\TConstitutionPlan;

class DeleteService
{
    /**
     * ### データ削除処理
     *
     * @param array $param
     */
    public function execDelete($param){

        $tConstitutionPlan = TConstitutionPlan::where('lp_order_id', $param['lp_order_id'])->first();

        if($tConstitutionPlan){
            TConstitutionPlan::where('lp_order_id', $param['lp_order_id'])
                ->firstOrFail()
                ->delete();
        }

    }
}