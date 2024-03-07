<?php

namespace App\Services\ConstitutionPlan;

use Illuminate\Support\Facades\DB;
use App\Models\TConstitutionPlan;
use App\Models\TPlanImage;
use App\Models\TPlanImageMemo;
use Illuminate\Support\Facades\Storage;
use Exception;
use App\Models\TConstitutionProcess;
class ShowService
{

    public function __construct(){}

    /**
     * 構成案情報の検索
     *
     * @param [type] $lp_order_id
     * @return void
     */
    public function exec($lp_order_id)
    {
        $result = [];
        $constitution_plan = [];
        $images= [];
        $memos= [];
        $plan_length = 12;
        // 構成案の取得
        $plans = TConstitutionPlan::where('lp_order_id','=',$lp_order_id)->orderBy('sort_order')->get()->each(function($plan){
            $plan->file = '';
        });
        $constitution_plan = $plans->toArray();
        if(!empty($constitution_plan)){
            // 構成案画像の取得
            $images = $plans->map(function($plan){
                $image = $plan->planImage->sortBy('sort_order')->values()->toArray();
                if(empty($image)){
                    array_push($image,[ 'id'=>null, 'file' => '', 'image_path' => '', ]);
                }
                return $image;
            })->toArray();
            // 画像メモの取得
            $memos = $plans->map(function($plan){
                $memo = $plan->planImageMemo->sortBy('sort_order')->values()->toArray();
                if(empty($memo)){
                    $memo = [
                        [ 'id'=>null, 'memo_category' => '大', 'memo' => '', ],
                        [ 'id'=>null, 'memo_category' => 'テキスト', 'memo' => '', ],
                        [ 'id'=>null, 'memo_category' => 'アイコン', 'memo' => '', ]
                    ];
                }
                return $memo;
            })->toArray();
        }
        // t_constitution_plansの初期値を12個設ける
        for($i=count($constitution_plan); $i<12; $i++){
            array_push($constitution_plan,
                [ 'id'=>null, 'block_detail' => '', 'requester_fix' => '', 'pharmaceutical_affairs_fix' => '', 'information_management_memo' => '', "image_path"=>'' , "file"=>'']
            );
        }
        // t_constitution_plans.block_detailが全て空の場合、構成の手順からもってくる
        $block_details = collect($constitution_plan)->filter(function($block){
            return !empty($block['block_detail']);
        })->all();
        if(empty($block_details)){
            $constitution_plan = $this->process_search($lp_order_id,$constitution_plan);
        }
        if(count($constitution_plan) > 12){
            $plan_length = count($constitution_plan);
        }
        // t_plan_imagesの初期値を12個設ける
        for($i=count($images); $i<$plan_length; $i++){
            array_push($images,[
                [ 'id'=>null, 'file' => '', 'image_path' => '', ]
            ]);
        }

        // t_plan_image_memosの初期値を12個設ける
        for($i=0; $i<$plan_length; $i++){
            if(!isset($memos[$i])){
                array_push($memos,[
                    [ 'id'=>null, 'memo_category' => '大', 'memo' => '', ],
                    [ 'id'=>null, 'memo_category' => 'テキスト', 'memo' => '', ],
                    [ 'id'=>null, 'memo_category' => 'アイコン', 'memo' => '', ]
                ]);
            }
        }
        return compact('constitution_plan','images','memos');
    }

    private function process_search($lp_order_id,$constitution_plan){
        $process = TConstitutionProcess::where('lp_order_id', $lp_order_id)->first();
        if(empty($process)) return $constitution_plan;
        $fix_blocks = $process->tConstitutionFixBlocks->sortBy('sort_order')->values()->toArray();
        if(!empty($fix_blocks)) return $this->process_to_plan($fix_blocks,$constitution_plan);
        $blocks = $process->tConstitutionBlocks->sortBy('sort_order')->values()->toArray();
        return $this->process_to_plan($blocks,$constitution_plan);
    }

    private function process_to_plan($blocks,$constitution_plan){
        foreach($blocks as $key => $block){
            if(isset($constitution_plan[$key])){
                $constitution_plan[$key]['block_detail'] = $block['block_detail'];
            }else{
                array_push($constitution_plan,
                    [
                        'id'=>null,
                        'block_detail' => $block['block_detail'],
                        'requester_fix' => '',
                        'pharmaceutical_affairs_fix' => '',
                        'information_management_memo' => '',
                        "image_path"=>'' ,
                        "file"=>''
                    ]
                );
            }
        }
        return $constitution_plan;
    }

}