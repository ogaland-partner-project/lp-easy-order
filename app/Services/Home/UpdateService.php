<?php

namespace App\Services\Home;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Exceptions\AppException;
use App\Models\TLpOrder;
use App\Http\Requests\LpOrderUpdateRequest;
use Carbon\Carbon;

/**
 * LP構成の更新
 * 機能ID: F-00001-00003
 */
class UpdateService
{

    /**
     * 更新
     *
     * @param int $lpOrderId
     * @param object $request
     * @return $rows
     */
    public function update($lpOrderId, LpOrderUpdateRequest $request)
    {

        try {
            DB::beginTransaction();

            // 更新元データ取得⇒更新
            $row = TLpOrder::find($lpOrderId);
            $row->fill($request->all())->save();

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }

    }

}
