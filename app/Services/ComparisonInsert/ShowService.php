<?php

namespace App\Services\ComparisonInsert;

use Illuminate\Support\Facades\DB;
use App\Models\TComparisonInsertHeader;
use App\Models\TComparisonInsertItem;
use Illuminate\Support\Facades\Storage;
use Exception;

class ShowService
{

    public function __construct(){}

    /**
     * ## 他社比較入力画面更新処理
     *
     * 1. t_companies_comparison_headers の検索
     * 1. t_comparison_insert_items の検索
     *
     * @return void
     */
    public function exec($lp_order_id)
    {
        $result = [];
        // ヘッダーの検索
        $headers = TComparisonInsertHeader::where('lp_order_id','=',$lp_order_id)->orderBy('sort_order')->get()->each(function($head){
            // セルの計算先がカンマ区切りで入っているので配列に変換
            $head->calculation_row = array_map("intval",explode(',',$head->calculation_row));
            // 空で保存されているデータはexplodeで0が入ってしまうので、空配列を挿入(画面側で配列に数値を入力するため)
            if($head->calculation_row[0] == 0){
                $head->calculation_row = [];
            }
            return $head;
        });

        // 連番付与処理
        // ヘッダー名が{serial_number}の場合、連番を付与。
        $serial_number = 1;
        $result['headers'] = $headers->toArray();
        $result['headers'] = collect($result['headers'])->map(function($header)use(&$serial_number){
            if($header['header_name'] == '{serial_number}'){
                $header['serial_number'] = $serial_number;
                $serial_number++;
            }
            return $header;
        });

        // ヘッダーに紐づくテーブル情報の取得
        $items = $headers->map(function($head){
            return $head->comparisonInsertItem->sortBy('col')->each(function($item)use($head){
                $item->comparison_insert_flag = $head->comparison_insert_flag;
                $item->companies_comparison_flag = $head->companies_comparison_flag;
                $item->editable = false;
            })->toArray();
        })->toArray();
        // 行と列を入れ替える
        if(!empty($items)) $items = array_map(null,...$items);
        $result['items'] = $items;
        return $result;
    }
}