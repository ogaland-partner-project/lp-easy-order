<?php

namespace App\Services\Lock;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Common\CommonMsg;
use App\Models\TLpOrderLock;

class DeleteService
{
    /**
     * 編集ロック解除
     *
     * @param [type] $id
     * @return void
     */
    public function delete($data)
    {
        try {
            DB::beginTransaction();
            $lp_order = TLpOrderLock::where('lp_order_id', $data['lp_order_id'])->where('menu_id', $data['menu_id']);
            if (empty($lp_order)) return;
            $lp_order->delete();
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }
}
