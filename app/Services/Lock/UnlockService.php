<?php

namespace App\Services\Lock;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Common\CommonMsg;
use App\Models\TLpOrderLock;

class UnlockService
{
    /**
     * 編集ロック強制解除
     *
     * @param [type] $lp_order_id
     * @return void
     */
    public function unlock($lp_order_id)
    {
        try {
            DB::beginTransaction();
            TLpOrderLock::where('lp_order_id',$lp_order_id)->delete();
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }
}
