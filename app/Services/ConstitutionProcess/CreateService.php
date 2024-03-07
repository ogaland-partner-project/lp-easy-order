<?php

namespace App\Services\ConstitutionProcess;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Exceptions\AppException;
use App\Models\TConstitutionProcess;
use App\Models\TConstitutionBlock;
use App\Models\TConstitutionFixBlock;
use App\Http\Requests\ConstitutionProcessCreateRequest;

/**
 * 構成の手順の各項目の新規登録
 * 機能ID: F-00007-00002
 */
class CreateService
{

    /**
     * 新規登録
     *
     * @param object $request
     */
    public function insert(ConstitutionProcessCreateRequest $request)
    {
        try {
            DB::beginTransaction();

            // 構成の手順の登録
            $row = new TConstitutionProcess();
            $row->fill($request->all());
            $row->save();

            $id = $row['id'];
            $blocks = $request->all();

            $i = 1;
            foreach($blocks['constitution_blocks_list'] as $block) {
                $row_bl = new TConstitutionBlock();
                $row_bl->constitution_process_id = $id;
                $row_bl->block_detail = $block['block_detail'];
                $row_bl->sort_order = $i;
                $row_bl->save();
                $i++;
            }

            $j = 1;
            foreach($blocks['constitution_fix_blocks_list'] as $fix_block) {
                $row_bl_fix = new TConstitutionFixBlock();
                $row_bl_fix->constitution_process_id = $id;
                $row_bl_fix->block_detail = $fix_block['block_detail'];
                $row_bl_fix->sort_order = $j;
                $row_bl_fix->save();
                $j++;
            }

            DB::commit();
        } catch (Exception $ex) {
            DB::rollback();
            throw $ex;
        }

    }

}
