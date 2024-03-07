<?php

namespace App\Services\LevelSelect;

use App\Models\TLevelSelect;

class DeleteService
{
    /**
     * ## データ削除処理
     *
     * @param [type] $request
     * @return void
     */
    public function execDelete($request){

        $tLevelSelect = TLevelSelect::where('lp_order_id', $request['lp_order_id'])->first();

        if ($tLevelSelect) {
            TLevelSelect::where('lp_order_id',$request['lp_order_id'])
            ->firstOrFail()
            ->delete();
        }

    }

}