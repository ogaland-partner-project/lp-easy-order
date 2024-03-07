<?php

namespace App\Services\ComparisonInsert;

use Illuminate\Support\Facades\DB;
use App\Models\TComparisonInsertHeader;
use App\Models\TComparisonInsertItem;
use Illuminate\Support\Facades\Storage;
use Exception;

class UpdateService
{

    public function __construct(){}

    /**
     * ## 他社比較入力画面更新処理
     *
     * 1. t_companies_comparison_headers の編集
     * 1. t_comparison_insert_items の編集
     *
     * @return void
     */
    public function exec($request,$lp_order_id)
    {
        DB::beginTransaction();
        try{
            // ヘッダー更新処理
            $ids = $this->HeaderEdit($request,$lp_order_id);
            // セル更新処理
            $this->ItemEdit($request,$ids,$lp_order_id);
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * ヘッダー情報の登録、更新、削除
     *
     * 1. t_companies_comparison_headers の登録、更新処理を下記条件で行う
     * * リクエストパラメータのidがnullの場合
     *    * t_companies_comparison_headers の登録処理
     * * リクエストパラメータのidに数値が入っている場合
     *    * t_companies_comparison_headers の更新処理
     *
     * 1.リクエストパラメータのid群とt_companies_comparison_headersのid群を比較し削除処理を行う
     *
     * @param [type] $request
     * @return void
     */
    private function HeaderEdit($request,$lp_order_id){
        $headers = $request['headers'];
        $ids = [];
        // ヘッダーの登録更新
        foreach($headers as $index => $header){
            $model = new TComparisonInsertHeader();
            // リクエストパラメータにIDが存在しない場合は新規登録
            if(empty($header['id'])){
                $insert_data = $model->create([
                    'lp_order_id' => $lp_order_id,
                    'header_name' => $header['header_name'],
                    'header_type' => $header['header_type'],
                    'calculation_type' => $header['calculation_type'],
                    'calculation_row' => implode(',',$header['calculation_row']),
                    'comparison_insert_flag' => $header['comparison_insert_flag'],
                    'companies_comparison_flag' => $header['companies_comparison_flag'],
                    'sort_order' => $index,
                    'created_pg' => 'ComparisonInsert.UpdateService.HeaderEdit',
                ]);
                array_push($ids,$insert_data->id);
            // リクエストパラメータにIDが存在している場合は更新
            }else{
                $item = $model::find($header['id']);
                $item->header_name = $header['header_name'];
                $item->header_type = $header['header_type'];
                $item->calculation_type = $header['calculation_type'];
                $item->calculation_row = implode(',',$header['calculation_row']);
                $item->comparison_insert_flag = $header['comparison_insert_flag'];
                $item->companies_comparison_flag = $header['companies_comparison_flag'];
                $item->sort_order = $index;
                $item->updated_pg = 'ComparisonInsert.UpdateService.HeaderEdit';
                $item->save();
                array_push($ids,$header["id"]);
            }
        }
        // 登録済みヘッダーのIDを取得
        $table_ids = collect(TComparisonInsertHeader::select('id')->where('lp_order_id',$lp_order_id)->get());
        $table_ids = $table_ids->map(function($ids){
            return $ids['id'];
        })->toArray();
        // 登録済みヘッダーに存在していてリクエストパラメータに存在していないヘッダーをテーブルから削除
        $diff = array_diff($table_ids,$ids);
        if(!empty($diff)){
            TComparisonInsertHeader::destroy($diff);
        }

        return $ids;
    }

    /**
     * テーブル情報の登録、更新、削除
     *
     * 1. t_companies_comparison_items の登録、更新処理を下記条件で行う
     * * リクエストパラメータのidがnullの場合
     *    * t_companies_comparison_items の登録処理
     * * リクエストパラメータのidに数値が入っている場合
     *    * t_companies_comparison_items の更新処理
     * * リクエストパラメータにfileがあれば画像の保存処理
     *
     * 1.リクエストパラメータのid群とt_companies_comparison_itemsのid群を比較し削除処理を行う
     *
     * @param [type] $request
     * @param [type] $ids
     * @return void
     */
    private function ItemEdit($request,$ids,$lp_order_id)
    {
        $items = $request['items'];
        $registered_id = [];
        // 各列ごとに保存処理を行う
        foreach($items as $col => $item){
            foreach($item as $index => $val){
                $model = new TComparisonInsertItem();
                // 画像のファイルがある場合は画像の保存処理
                if(array_key_exists("file", $val)){
                    $val['text'] = $val['path'];
                    $path = "/public/lp_order/".$lp_order_id."/ComparisonInsert/";
                    Storage::putFileAs($path,$val['file'],$val['id'].$val['extension']);
                }
                // パラメータにIDが存在しない場合は登録処理
                if(empty($val['id'])){
                    $id = $ids[$index];
                    $insert_data = $model->create([
                        'comparison_header_id' => $id,
                        'text' => $val['text'],
                        'color' => $val['color'],
                        'col' => $col,
                        'created_pg' => 'ComparisonInsert.UpdateService.HeaderEdit',
                    ]);
                    array_push($registered_id,$insert_data->id);
                // パラメータにIDが存在する場合は更新処理
                }else{
                    $item = $model::find($val['id']);
                    $item->text = $val['text'];
                    $item->color = $val['color'];
                    $item->col = $col;
                    $item->save();
                    array_push($registered_id,$val['id']);
                }
            }
        }
        // 登録済みテーブル情報のIDを取得
        $table_ids = TComparisonInsertItem::select('id')->whereHas('comparisonInsertHeader',function($q)use($lp_order_id){
            $q->where('lp_order_id','=',$lp_order_id);
        })->get();
        $table_ids = collect($table_ids);
        $table_ids = $table_ids->map(function($ids){
            return $ids['id'];
        })->toArray();
        // 登録済みテーブル情報に存在していてリクエストパラメータに存在していないテーブル情報をテーブルから削除
        $diff = array_diff($table_ids,$registered_id);
        if(!empty($diff)){
            TComparisonInsertItem::destroy($diff);
        }
    }

}