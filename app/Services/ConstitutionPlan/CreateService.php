<?php

namespace App\Services\ConstitutionPlan;

use Illuminate\Support\Facades\DB;
use App\Models\TConstitutionPlan;
use App\Models\TPlanImage;
use App\Models\TPlanImageMemo;
use Illuminate\Support\Facades\Storage;
use Exception;
use App\Services\Common\CommonService;

class CreateService
{

    public function __construct(){}

    /**
     * ## 構成案新規作成処理
     *
     * 1. t_constitution_plans への登録
     * 1. t_plan_images への登録,画像の保存処理
     * 1. t_plan_image_memos への登録
     *
     * @return void
     */
    public function exec($param)
    {
        DB::beginTransaction();
        try{
            // 構成案の新規登録
            $ids = $this->PlanInsert($param);
            // 構成案画像情報の新規登録、画像の保存処理
            $this->PlanImageInsert($param,$ids);
            // 構成案画像メモの新規登録
            $this->PlanImageMemoInsert($param,$ids);
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * ## t_constitution_plans への登録
     *
     * * 構成案を新規登録し、各構成案のIDを取得
     *
     * @param [type] $param
     * @return void
     */
    private function PlanInsert($param){
        $ids = [];
        foreach($param['constitution_plan'] as $key => $value){
            $model = new TConstitutionPlan();
            $data = $model->create([
                'lp_order_id' => $param['lp_order_id'],
                'block_detail' => $value['block_detail'],
                'requester_fix' => $value['requester_fix'],
                'pharmaceutical_affairs_fix' => $value['pharmaceutical_affairs_fix'],
                'information_management_memo' => $value['information_management_memo'],
                'sort_order' => $key,
                'created_pg' => 'CreateService.PlanInsert'
            ]);
            // リレーション先の外部キー用にidを取得
            array_push($ids,$data->id);
        }
        return $ids;
    }

    /**
     * ## t_plan_images への登録,画像の保存処理
     *
     * 1. t_plan_imagesへ登録処理
     * 1. 登録したデータのidを元に画像の保存先パスを作成しテーブルを更新
     * 1. 保存先パスを元に画像の保存処理
     *
     * @param [type] $ids
     * @return void
     */
    private function PlanImageInsert($param,$ids)
    {
        // 画像の保存先にテーブルのidが必要なため、保存処理の後に更新を行う
        foreach($param['image_paths'] as $key => $value){
            foreach($value as $sort => $val){
                $model = new TPlanImage();
                // 新規登録処理
                $insert_data = $model->create([
                    'constitution_plan_id' => $ids[$key],
                    'image_path' => null,
                    'sort_order' => $sort,
                    'created_pg' => 'CreateService.PlanImageInsert'
            ]);
                // 画像パス保存,画像保存処理
                $path_insert = $model::find($insert_data->id);
                $common = new CommonService();
                $common->saveImageFiletFromParamString($path_insert,$param['lp_order_id'],$val['file'],"ConstitutionPlan","image_path",null);
            }
        }
    }

    /**
     * t_plan_image_memos への登録処理
     *
     * @param [type] $param
     * @param [type] $ids
     * @return void
     */
    private function PlanImageMemoInsert($param,$ids)
    {
        foreach($param['memos'] as $key => $value){
            foreach($value as $sort => $val){
                $model = new TPlanImageMemo();
                $model->create([
                    'constitution_plan_id' => $ids[$key],
                    'memo' => $val['memo'],
                    'memo_category' => $val['memo_category'],
                    'sort_order' => $sort,
                    'created_pg' => 'CreateService.PlanImageMemoInsert'
                ]);
            }
        }
    }

}