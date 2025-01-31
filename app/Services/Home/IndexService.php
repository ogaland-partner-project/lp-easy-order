<?php

namespace App\Services\Home;

use App\Models\TLpOrder;
use App\Http\Resources\LpOrderResource;

/**
 * LP構成の一覧を検索
 * 機能ID: F-00001-00001
 */
class IndexService
{

    /**
     * 検索
     *
     * @return $rows
     */
    public function getAll()
    {

        $rows = TLpOrder::with(['tLpOrderLocks','tLevelSelects'])->orderBy('id','desc')->get();
        return LpOrderResource::collection($rows);

    }

}
