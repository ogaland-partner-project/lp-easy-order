<?php

namespace App\Services\Lock;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Common\CommonMsg;
use App\Models\TLpOrderLock;

class CreateService
{
    /**
     * 編集ロック
     *
     * @param array $param
     */
    public function execCreate($param)
    {
        $param = collect($param)->toArray();
        try {
            DB::beginTransaction();
            $lock_table = new TLpOrderLock();
            $lock_table->fill($param);
            $lock_table->save();
            DB::commit();
            return $lock_table->id;
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }
}
