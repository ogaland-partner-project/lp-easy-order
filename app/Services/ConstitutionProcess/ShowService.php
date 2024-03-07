<?php

namespace App\Services\ConstitutionProcess;

use Exception;
use App\Exceptions\AppException;
use App\Models\TConstitutionProcess;
use App\Http\Resources\ConstitutionProcessResource as ConstitutionProcessResource;

/**
 * 構成の手順の各項目を検索
 * 機能ID: F-00007-00001
 */
class ShowService
{

    /**
     * 検索
     *
     * @param int $lpOrderId
     * @return $rows
     */
    public function findById($lpOrderId)
    {
        $result = [];
        $result['concept_word'] = "";
        $catchphrases = [];
        $how_blocks = [];
        $blocks = [];
        $fix_blocks = [];

        $process = TConstitutionProcess::where('lp_order_id', $lpOrderId)->first();
        // t_constitution_processの値が存在する場合はリレーション先のデータを取得
        if(!empty($process)){
            $result['concept_word'] = $process['concept_word'];
            // t_constitution_catchphraseの取得
            $catchphrases = $process->tConstitutionCatchphrases->sortBy('sort_order')->toArray();
            // t_constitution_how_blockの取得
            $how_blocks = $process->tConstitutionHowBlocks->each(function($block){
                // 画面上で別ブロックへのコピー時にタイプが必要になるので挿入
                $block->type = "how";
            })->sortBy('sort_order')->values()->toArray();
            // t_constitution_blocksの取得
            $blocks = $process->tConstitutionBlocks->each(function($block){
                // 画面上で別ブロックへのコピー時にタイプが必要になるので挿入
                $block->type = "block";
            })->sortBy('sort_order')->values()->toArray();
            // t_constitution_fix_blocksの取得
            $fix_blocks = $process->tConstitutionFixBlocks->each(function($block){
                // 画面上で別ブロックへのコピー時にタイプが必要になるので挿入
                $block->type = "fix";
            })->sortBy('sort_order')->values()->toArray();
        }

        //t_constitution_catchphraseの初期値を15個設ける
        for($i=count($catchphrases); $i < 15; $i++){
            array_push($catchphrases,[ "id"=>null, "catchphrase"=>"" ]);
        }
        $result['constitution_catchphrase_list'] = $catchphrases;

        //t_constitution_how_blockの初期値を20個設ける
        for($i=count($how_blocks); $i < 20; $i++){
            array_push($how_blocks,[ "id"=>null, "block_detail"=>"", "type"=>"how" ]);
        }
        $result['constitution_how_blocks_list'] = $how_blocks;

        //t_constitution_blocksの初期値を20個設ける
        for($i=count($blocks); $i < 20; $i++){
            array_push($blocks,[ "id"=>null, "block_detail"=>"", "type"=>"block" ]);
        }
        $result['constitution_blocks_list'] = $blocks;

        //t_constitution_fix_blocksの初期値を20個設ける
        for($i=count($fix_blocks); $i < 20; $i++){
            array_push($fix_blocks,[ "id"=>null, "block_detail"=>"", "type"=>"fix" ]);
        }
        $result['constitution_fix_blocks_list'] = $fix_blocks;
        return $result;
        // return ConstitutionProcessResource::collection($result);

    }

}
