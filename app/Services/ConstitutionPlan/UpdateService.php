<?php

namespace App\Services\ConstitutionPlan;

use Illuminate\Support\Facades\DB;
use App\Models\TConstitutionPlan;
use App\Models\TPlanImage;
use App\Models\TPlanImageMemo;
use Illuminate\Support\Facades\Storage;
use Exception;
use App\Services\Common\CommonService;
use PhpParser\Node\Stmt\Continue_;

class UpdateService
{

    public function __construct(){}

    /**
     * ## 構成案更新処理
     *
     * 1. t_constitution_plans の登録、更新
     * 1. t_plan_images への登録、更新、画像の保存処理
     * 1. t_plan_image_memos への登録更新
     *
     * @return void
     */
    public function exec($lp_order_id,$param)
    {
        DB::beginTransaction();
        try{
            $plan_ids = $this->PlanUpdate($lp_order_id,$param);
            $this->PlanImageUpdate($param,$plan_ids);
            $this->PlanImageMemoUpdate($param,$plan_ids);
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * ## t_constitution_plans の登録更新削除
     *
     * 1. t_constitution_plans の登録、更新処理を下記条件で行う
     * * リクエストパラメータのidがnullの場合
     *    * t_constitution_plans の登録処理
     * * リクエストパラメータのidに数値が入っている場合
     *    * t_constitution_plans の更新処理
     *
     * 1.リクエストパラメータのid群とt_constitution_plansのid群を比較し削除処理を行う
     *
     * @param [type] $param
     * @return void
     */
    private function PlanUpdate($lp_order_id,$param){
        $plan_ids = [];
        $this->execRowDelete(new TConstitutionPlan(),'lp_order_id',$lp_order_id,$param['constitution_plan']);
        foreach($param['constitution_plan'] as $key => $value){
            $model = new TConstitutionPlan();
            if(empty($value['id'])){
                $model_data = $model->create([
                    'lp_order_id' => $lp_order_id,
                    'block_detail' => $value['block_detail'],
                    'requester_fix' => $value['requester_fix'],
                    'pharmaceutical_affairs_fix' => $value['pharmaceutical_affairs_fix'],
                    'information_management_memo' => $value['information_management_memo'],
                    'sort_order' => $key,
                    'created_pg' => 'CreateService.PlanUpdate'
                ]);
                array_push($plan_ids,$model_data->id);
            }else{
                $model_data = $model::find($value['id']);
                $model_data->block_detail = $value['block_detail'];
                $model_data->requester_fix = $value['requester_fix'];
                $model_data->pharmaceutical_affairs_fix = $value['pharmaceutical_affairs_fix'];
                $model_data->information_management_memo = $value['information_management_memo'];
                $model_data->image_path = $value['image_path'];
                $model_data->sort_order = $key;
                $model_data->updated_pg = "UpdateService.PlanUpdate";
                $model_data->save();
                array_push($plan_ids,$value['id']);
            }
            if(!empty($value['file'])){
                // 画像パス保存処理
                $common = new CommonService();
                $common->saveImageFiletFromParamString($model_data,$lp_order_id,$value['file'],"ConstitutionPlan","image_path","_memo");
            }
        }
        return $plan_ids;
    }

    /**
     * ## t_plan_images への登録更新削除,画像の保存処理
     *
     * 1. t_plan_images の登録、更新処理を下記条件で行う
     * * リクエストパラメータのidがnullの場合
     *    * t_plan_images の登録処理、画像の保存
     * * リクエストパラメータのidに数値が入っている場合
     *    * t_plan_images の更新処理
     *
     * 1.リクエストパラメータのid群とt_constitution_plansのid群を比較し削除処理を行う
     *
     * @param [type] $plan_ids
     * @return void
     */
    private function PlanImageUpdate($param,$plan_ids)
    {
        foreach($param['image_paths'] as $key => $value){
            // 削除処理
            $this->execRowDelete(new TPlanImage(),'constitution_plan_id',$plan_ids[$key],$value);
            foreach($value as $sort => $val){
                // パラメータにIDが存在する場合は更新、ない場合は新規登録
                $model = new TPlanImage();
                if(empty($val['id'])){
                    if(empty($val['file'])) continue;
                    // 画像の保存先にテーブルのidが必要なため、保存処理の後に更新を行う
                    $insert_data = $model->create([
                        'constitution_plan_id' => $plan_ids[$key],
                        'image_path' => null,
                        'sort_order' => $sort,
                        'created_pg' => 'CreateService.PlanImageInsert'
                    ]);
                    // 画像パス保存処理
                    $common = new CommonService();
                    $common->saveImageFiletFromParamString($insert_data,$param['lp_order_id'],$val['file'],"ConstitutionPlan","image_path",null);
                }else{
                    // 更新処理
                    $update_data = $model::find($val['id']);
                    $update_data->image_path = $val['image_path'];
                    $update_data->sort_order = (int)$sort;
                    $update_data->save();
                }
            }
        }
    }

    /**
     * t_plan_image_memos への登録更新削除
     *
     * 1. t_plan_image_memos の登録、更新処理を下記条件で行う
     * * リクエストパラメータのidがnullの場合
     *    * t_plan_image_memos の登録処理
     * * リクエストパラメータのidに数値が入っている場合
     *    * t_plan_image_memos の更新処理
     *
     * 1.リクエストパラメータのid群とt_constitution_plansのid群を比較し削除処理を行う
     *
     * @param [type] $param
     * @param [type] $plan_ids
     * @return void
     */
    private function PlanImageMemoUpdate($param,$plan_ids)
    {
        foreach($param['memos'] as $key => $value){
            // 削除処理
            $this->execRowDelete(new TPlanImageMemo(),'constitution_plan_id',$plan_ids[$key],$value);
            foreach($value as $sort => $val){
                $model = new TPlanImageMemo();
                if(empty($val['id'])){
                    if(empty($val['memo'])) continue;
                    $model->create([
                        'constitution_plan_id' => $plan_ids[$key],
                        'memo' => $val['memo'],
                        'memo_category' => $val['memo_category'],
                        'sort_order' => $sort,
                        'created_pg' => 'CreateService.PlanImageMemoInsert'
                    ]);
                }else{
                    $update_plan = $model::find($val['id']);
                    $update_plan->memo = $val['memo'];
                    $update_plan->memo_category = $val['memo_category'];
                    $update_plan->sort_order = $sort;
                    $update_plan->updated_pg = "UpdateService.PlanUpdate";
                    $update_plan->save();
                }
            }
        }
    }

    // 各モデルの削除処理
    public function execRowDelete($model,$where_column,$target_id,$request_array){
        $registered_model = $model::where($where_column,$target_id);
        $insert_ids = collect($request_array)->pluck('id')->toArray();
        $insert_ids = array_filter($insert_ids,function($id){return !is_null($id);});
        $registered_model->whereNotIn('id',$insert_ids)->delete();
    }
}