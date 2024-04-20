<?php

namespace App\Services\ComparisonInsert;

use Illuminate\Support\Facades\DB;
use App\Models\TComparisonInsertHeader;
use App\Models\TComparisonInsertItem;
use Illuminate\Support\Facades\Storage;
use Exception;
use App\Exceptions\AppException;
use App\Common\CommonMsg;

class CreateService
{

    public function __construct(){}

    /**
     * ## 他社比較入力画面新規作成処理
     *
     * 1. t_companies_comparison_headers への登録
     * 1. t_comparison_insert_items への登録
     *
     * @return void
     */
    public function exec($param)
    {
        DB::beginTransaction();
        try{
            // 既存データがある場合は削除
            $this->delete($param);
            $ids = $this->HeaderInsert($param);
            $this->ItemInsert($ids);
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 既存データの削除
     *
     * @param [type] $request
     * @return void
     */
    private function delete($param){
        $otherHeaders = TComparisonInsertHeader::where('lp_order_id','=',$param['lp_order_id'])->get();
        if(!empty($otherHeaders)){
            TComparisonInsertHeader::where('lp_order_id',$param['lp_order_id'])->delete();
        }
    }

    /**
     * ヘッダー情報の登録処理
     *
     * @param [type] $param
     * @return void
     */
    private function HeaderInsert($param){
        $model = new TComparisonInsertHeader();
        $ids = [];
        foreach($param['headers'] as $key => $value){
            $calculation_type = null;
            $calculation_row = null;
            if($key == 8){
                $calculation_type = 'division';
                $calculation_row = $ids[6].','.$ids[7];
            }
            $data = $model->create([
                'lp_order_id' => $param['lp_order_id'],
                'header_name' => $value['header_name'],
                'header_type' => $value['header_type'],
                'calculation_type' => $calculation_type,
                'calculation_row' => $calculation_row,
                'comparison_insert_flag' => $value['comparison_insert_flag'],
                'companies_comparison_flag' => $value['companies_comparison_flag'],
                'sort_order' => $key,
            ]);
            array_push($ids,$data->id);

        }
        for($i=1;$i<=30;$i++){
            $data = $model->create([
                'lp_order_id' => $param['lp_order_id'],
                'header_name' => "{serial_number}",
                'header_type' => "text",
                'calculation_type' => null,
                'calculation_row' => null,
                'comparison_insert_flag' => 0,
                'companies_comparison_flag' => 1,
                'sort_order' => $key,
            ]);
            array_push($ids,$data->id);
        }
        return $ids;
    }

    /**
     * テーブル情報の登録処理
     *
     * @param [type] $ids
     * @return void
     */
    private function ItemInsert($ids)
    {
        $model = new TComparisonInsertItem();
        // 初期登録として各列3回繰り返す
        for($i=0; $i<5; $i++){
            foreach($ids as $index => $id){
                $text = null;
                if($i == 0 && $index == 0){
                    $text = 'ベンチマーク';
                }
                if($i == 4 && $index == 0){
                    $text = '自社製品';
                }
                $model->create([
                    'comparison_header_id' => $id,
                    'text' => $text,
                    'color' => "#FFFFFFFF",
                    'col' => $i
                ]);
            }
        }
    }

}