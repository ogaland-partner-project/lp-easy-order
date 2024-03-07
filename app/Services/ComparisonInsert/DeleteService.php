<?php

namespace App\Services\ComparisonInsert;

use App\Models\TComparisonInsertHeader;

class DeleteService
{
    /**
     * ## データ削除処理
     *
     * @param [type] $request
     * @return void
     */
    public function execDelete($request){

        $tComparisonInsertHeader = TComparisonInsertHeader::where('lp_order_id', $request['lp_order_id'])->first();

        if($tComparisonInsertHeader){
            TComparisonInsertHeader::where('lp_order_id',$request['lp_order_id'])
                ->firstOrFail()
                ->delete();
        }
    }

}