<?php

namespace App\Services\ConstitutionProcess;

use App\Models\TConstitutionProcess;

class DeleteService
{
    /**
     * ### データ削除処理
     *
     * @param array $param
     */
    public function execDelete($param){

        $tConstitutionProcess = TConstitutionProcess::where('lp_order_id', $param['lp_order_id'])->first();

        if($tConstitutionProcess){
            TConstitutionProcess::where('lp_order_id', $param['lp_order_id'])
                ->firstOrFail()
                ->delete();
        }
    }
}
