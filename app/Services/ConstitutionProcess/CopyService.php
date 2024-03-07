<?php

namespace App\Services\ConstitutionProcess;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Exceptions\AppException;
use App\Common\CommonMsg;
use App\Services\Common\CommonService;
use App\Models\TConstitutionProcess;
use App\Services\ConstitutionProcess\DeleteService;

/**
 * 構成の手順を他の構成からコピー作成
 * 機能ID: F-00007-00005
 */
class CopyService extends CommonService
{

    public function __construct(){}

    /**
     * ## 構成案新規作成処理
     * 1. LP構成のデータ活性状態確認（コピー先）
     * 1. LP構成のデータ活性状態確認（コピー元）
     * 1. 関連テーブルの活性確認
     * 1. 既存データ削除
     *   - t_constitution_processes の既存データ削除
     *      - t_constitution_catchphrases の既存データも連動して削除
     *      - t_constitution_how_blocks の既存データも連動して削除
     *      - t_constitution_blocks の既存データも連動して削除
     *      - t_constitution_fix_blocks の既存データも連動して削除
     * 1. t_constitution_processes のコピー処理
     *      - t_constitution_catchphrases の既存データも連動して削除
     *      - t_constitution_how_blocks の既存データも連動して削除
     *      - t_constitution_blocks の既存データも連動して削除
     *      - t_constitution_fix_blocks の既存データも連動して削除
     *
     * @return void
     */
    public function exec($param)
    {
        DB::beginTransaction();
        try{
            // * LP構成のデータ活性状態確認（コピー先）
            $this->copyAbleCheckLpOrder($param['lp_order_id']);

            // * LP構成のデータ活性状態確認（コピー元）
            $this->copyAbleCheckLpOrder($param['other_lp_order_id']);

            // * 関連テーブルの活性確認
            $this->copyAbleCheckRelation($param);

            // * 既存データ削除
            $delService = new DeleteService;
            $delService->execDelete($param);

            // * データ複製
            $this->copyDataRelation($param);

            DB::commit();

        }catch(Exception $e){
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * ### 関連テーブルのコピー元データ活性確認
     *
     * @param array $param
     */
    public function copyAbleCheckRelation($param){

        $ConstitutionProcess = TConstitutionProcess::where('lp_order_id', $param['other_lp_order_id']);

        if (!$ConstitutionProcess->exists()) {
            throw new AppException(CommonMsg::MSG_ID_000006);
        }
    }

    /**
     * ## 構成案データのコピー処理
     *
     * 1. パラメータのother_lp_order_idに紐づく情報をコピー
     *
     * @param [type] $param
     * @return void
     */
    public function copyDataRelation($param){

        // コピー元データ取得
        $processes = TConstitutionProcess::where('lp_order_id', $param['other_lp_order_id']);

        // データを複製する＆一部項目は編集
        $processes->each(function($otherProcess)use($param){
            $process = $otherProcess->replicate();
            $process->lp_order_id = $param['lp_order_id'];
            $process->created_pg = 'ConstitutionProcess.CopyService.copyDataRelation';
            $process->updated_pg = 'ConstitutionProcess.CopyService.copyDataRelation';
            $process->save();
            $otherProcess->tConstitutionCatchphrases->each(function($otherCatchphrases) use($process){
                $catchphrases = $otherCatchphrases->replicate();
                $catchphrases->constitution_process_id = $process->id;
                $catchphrases->created_pg = 'ConstitutionProcess.CopyService.copyDataRelation';
                $catchphrases->updated_pg = 'ConstitutionProcess.CopyService.copyDataRelation';
                $catchphrases->save();
            });
            $otherProcess->tConstitutionHowBlocks->each(function($otherHowBlocks) use($process){
                $howBlocks = $otherHowBlocks->replicate();
                $howBlocks->constitution_process_id = $process->id;
                $howBlocks->created_pg = 'ConstitutionProcess.CopyService.copyDataRelation';
                $howBlocks->updated_pg = 'ConstitutionProcess.CopyService.copyDataRelation';
                $howBlocks->save();
            });
            $otherProcess->tConstitutionBlocks->each(function($otherBlocks) use($process){
                $blocks = $otherBlocks->replicate();
                $blocks->constitution_process_id = $process->id;
                $blocks->created_pg = 'ConstitutionProcess.CopyService.copyDataRelation';
                $blocks->updated_pg = 'ConstitutionProcess.CopyService.copyDataRelation';
                $blocks->save();
            });
            $otherProcess->tConstitutionFixBlocks->each(function($otherFixBlocks) use($process){
                $fixBlocks = $otherFixBlocks->replicate();
                $fixBlocks->constitution_process_id = $process->id;
                $fixBlocks->created_pg = 'ConstitutionProcess.CopyService.copyDataRelation';
                $fixBlocks->updated_pg = 'ConstitutionProcess.CopyService.copyDataRelation';
                $fixBlocks->save();
            });
        });
    }
}
