<?php

namespace App\Services\ComparisonInsert;

use Illuminate\Support\Facades\DB;
use App\Models\TComparisonInsertHeader;
use Exception;
use App\Exceptions\AppException;
use App\Common\CommonMsg;
use App\Services\Common\CommonService;
use App\Services\ComparisonInsert\DeleteService;

class CopyService
{

    public function __construct(){}

    private $calculation_rows = [];
    private $ids = [];

    /**
     * ## 他社比較入力画面コピー処理
     *
     * 1. t_comparison_insert_headers の編集
     * 1. t_comparison_insert_items の編集
     *
     * @return void
     */
    public function exec($request)
    {

        DB::beginTransaction();
        try{
            $common_service = new CommonService();
            // コピー先の構成存在チェック
            $common_service->copyAbleCheckLpOrder($request->lp_order_id);
            // コピー元の構成存在チェック
            $common_service->copyAbleCheckLpOrder($request->other_lp_order_id);
            // コピー元データの存在チェック
            $this->copyAbleCheckRelation($request);
            // コピー先の削除処理
            $delService = new DeleteService;
            $delService->execDelete($request);
            // コピー処理
            $this->copyDataRelation($request);
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * コピー元データの存在チェック
     *
     * @param [type] $request
     * @return void
     */
    public function copyAbleCheckRelation($request){
        $otherHeaders = TComparisonInsertHeader::where('lp_order_id','=',(int)$request->other_lp_order_id)->get();
        if(empty($otherHeaders)){
            throw new AppException(CommonMsg::MSG_ID_000006);
        }
    }

    /**
     * ## 他社比較画面のコピー
     *
     * 1. パラメータのother_lp_order_idに紐づく情報をコピーして新規登録
     * 1. t_comparison_insert_headers.rowのidを新しいidに置き換える
     *    *└画面上でセルの計算に利用。計算元となる行のidが入っている。
     *    *└コピーするだけだとコピー元のidが入っているので、コピー先のidへ置き換える必要がある。
     *
     * @param [type] $request
     * @return void
     */
    public function copyDataRelation($request){

        $otherHeaders = TComparisonInsertHeader::where('lp_order_id','=',(int)$request['other_lp_order_id'])->get();
        if(empty($otherHeaders)){
            throw new AppException(CommonMsg::MSG_ID_000006);
        }
        $otherHeaders->each(function($otherHead) use($request){
            $header = $otherHead->replicate();
            $header->lp_order_id = $request['lp_order_id'];
            $header->created_pg = 'ComparisonInsert.CopyService.copyDataRelation';
            $header->updated_pg = 'ComparisonInsert.CopyService.copyDataRelation';
            $header->save();
            // コピー元のidとコピー先のidとを紐図ける為、配列に入れる
            array_push($this->ids,['old_id'=>$otherHead->id,'new_id'=>$header->id]);
            if(!empty($header->calculation_row)){
                // 後ほど変換するためコピー先のidと計算用配列を保持
                array_push($this->calculation_rows,['new_id'=>$header->id,'calculation_rows'=>explode(',',$header->calculation_row)]);
            }
            $otherHead->comparisonInsertItem->each(function($otherItem) use($header){
                $item = $otherItem->replicate();
                $item->comparison_header_id = $header->id;
                $item->created_pg = 'ComparisonInsert.CopyService.PlanCopy';
                $item->updated_pg = 'ComparisonInsert.CopyService.PlanCopy';
                $item->save();
            });
        });
        // t_comparison_insert_headers.rowの値がコピー元のidを指したままなので、コピー先のidに置き換える
        foreach($this->calculation_rows as $value){
            $rows = [];
            foreach($value['calculation_rows'] as $old_id){
                // コピー元のidからコピー先のidを検索し配列へ入れる
                $key = array_search($old_id,array_column($this->ids,'old_id'));
                array_push($rows,$this->ids[$key]['new_id']);
            }
            $model = new TComparisonInsertHeader();
            $update_data = $model::find($value['new_id']);
            $update_data->calculation_row = implode(',',$rows);
            $update_data->save();
        }
    }

}