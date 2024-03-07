<?php

namespace App\Services\ConstitutionProcess;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Exceptions\AppException;
use App\Models\TConstitutionProcess;
use App\Models\TConstitutionBlock;
use App\Models\TConstitutionFixBlock;
use App\Models\TConstitutionHowBlock;
use App\Models\TConstitutionCatchphrase;

/**
 * 構成の手順の各項目の更新
 * 機能ID: F-00007-00003
 */
class UpdateService
{

    /**
     * 更新
     *
     * @param int $lpOrderId
     * @param object $request
     */
    public function update($lpOrderId,$request)
    {

        try {
            DB::beginTransaction();
            // t_constitution_processesの更新対象データ取得⇒更新
            $row = TConstitutionProcess::where('lp_order_id', $lpOrderId)->first();
            if(empty($row)){
                $row = new TConstitutionProcess();
            }
            $row->lp_order_id = $lpOrderId;
            $row->concept_word = $request['concept_word'];
            $row->save();
            $constitution_process_id = $row['id'];

            // t_constitution_catchphraseへの更新処理
            // 削除処理
            $this->execRowDelete(new TConstitutionCatchphrase(),$constitution_process_id,$request['constitution_catchphrase_list']);
            // 更新処理
            foreach($request['constitution_catchphrase_list'] as $index => $catchphrase_block) {
                if(isset($catchphrase_block['id'])){
                    // 更新対象データの取得
                    $row_catchphrase = TConstitutionCatchphrase::where('id', $catchphrase_block['id'])->first();
                }else{
                    // 新規登録のためのid設定
                    if(empty($catchphrase_block['catchphrase'])) continue;
                    $row_catchphrase = new TConstitutionCatchphrase();
                    $row_catchphrase->constitution_process_id = $constitution_process_id;
                }
                $row_catchphrase->catchphrase = $catchphrase_block['catchphrase'];
                $row_catchphrase->sort_order = $index;
                $row_catchphrase->save();
            }

            // t_constitution_how_blockへの更新処理
            // 削除処理
            $this->execRowDelete(new TConstitutionHowBlock(),$constitution_process_id,$request['constitution_how_blocks_list']);
            // 更新処理
            foreach($request['constitution_how_blocks_list'] as $index => $how_block) {
                if(isset($how_block['id'])){
                    // 更新対象データの取得
                    $row_bl_how = TConstitutionHowBlock::where('id', $how_block['id'])->first();
                }else{
                    // 新規登録のためのid設定
                    if(empty($how_block['block_detail'])) continue;
                    $row_bl_how = new TConstitutionHowBlock();
                    $row_bl_how->constitution_process_id = $constitution_process_id;
                }
                $row_bl_how->block_detail = $how_block['block_detail'];
                $row_bl_how->sort_order = $index;
                $row_bl_how->save();
            }

            // t_constitution_blocksへの更新処理
            // 削除処理
            $this->execRowDelete(new TConstitutionBlock(),$constitution_process_id,$request['constitution_blocks_list']);
            // 更新処理
            foreach($request['constitution_blocks_list'] as $index => $block) {
                if(isset($block['id'])){
                    // 更新対象データの取得
                    $row_bl = TConstitutionBlock::where('id', $block['id'])->first();
                }else{
                    // 新規登録のためのid設定
                    if(empty($block['block_detail'])) continue;
                    $row_bl = new TConstitutionBlock();
                    $row_bl->constitution_process_id = $constitution_process_id;
                }
                $row_bl->block_detail = $block['block_detail'];
                $row_bl->sort_order = $index;
                $row_bl->save();
            }

            // t_constitution_fix_blocksへの更新処理
            // 削除処理
            $this->execRowDelete(new TConstitutionFixBlock(),$constitution_process_id,$request['constitution_fix_blocks_list']);
            // 更新処理
            foreach($request['constitution_fix_blocks_list'] as $index => $fix_block) {
                if(isset($fix_block['id'])){
                    // 更新対象データの取得
                    $row_bl_fix = TConstitutionFixBlock::where('id', $fix_block['id'])->first();
                }else{
                    // 新規登録のためのid設定
                    if(empty($fix_block['block_detail'])) continue;
                    $row_bl_fix = new TConstitutionFixBlock();
                    $row_bl_fix->constitution_process_id = $constitution_process_id;
                }
                $row_bl_fix->block_detail = $fix_block['block_detail'];
                $row_bl_fix->sort_order = $index;
                $row_bl_fix->save();
            }

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }

    }

    // 各モデルの削除処理
    public function execRowDelete($model,$constitution_process_id,$request_array){
        $registered_model = $model::where('constitution_process_id',$constitution_process_id);
        $insert_ids = collect($request_array)->pluck('id')->toArray();
        $insert_ids = array_filter($insert_ids,function($id){return !is_null($id);});
        $registered_model->whereNotIn('id',$insert_ids)->delete();
    }

}
