<?php

namespace App\Services\Home;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Exceptions\AppException;
use App\Models\TLpOrder;
use App\Http\Requests\LpOrderCreateRequest;
use Carbon\Carbon;

/**
 * LP構成の新規登録
 * 機能ID: F-00001-00002
 */
class CreateService
{

    /**
     *　新規登録
     *
     * @param object $request
     */
    public function insert(LpOrderCreateRequest $request)
    {

        try {

            DB::beginTransaction();

            // LP構成の登録
            $row = new TLpOrder();
            $row->fill($request->all());
            $row->save();

            DB::commit();
        } catch (Exception $ex) {
            DB::rollback();
            throw $ex;
        }

    }

}
