<?php

namespace App\Services\Lock;

use App\Models\TLpOrderLock;

class ShowService
{
    /**
     * 編集ロック確認
     *
     * @param array $param
     */
    public function show($param)
    {
        $param = collect($param)->toArray();
        if($param['menu_id'] == 4 || $param['menu_id'] == 5){
            // 他社比較入力と他社構成比較は同時編集させない処理
            $lock = TLpOrderLock::where('lp_order_id',$param['lp_order_id'])->whereIn('menu_id',[4,5])->exists();
            return $lock;
        }
        $lock = TLpOrderLock::where('lp_order_id',$param['lp_order_id'])->where('menu_id',$param['menu_id'])->exists();
        return $lock;
    }
}
