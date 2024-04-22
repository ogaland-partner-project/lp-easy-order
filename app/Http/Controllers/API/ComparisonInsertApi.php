<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ComparisonInsertCreateRequest;
use App\Http\Requests\ComparisonInsertUpdateRequest;
use App\Http\Requests\ComparisonInsertCopyRequest;
use App\Services\ComparisonInsert\CreateService;
use App\Services\ComparisonInsert\UpdateService;
use App\Services\ComparisonInsert\ShowService;
use App\Services\ComparisonInsert\CopyService;
use App\Common\CommonMsg;


/**
 * 他社比較入力画面のAPIクラス
 * @group ComparisonInsertApi
 */
class ComparisonInsertApi extends ApiController
{
    /**
     * 他社比較入力テーブル情報の検索(機能ID：F-00005-00005)
     *
     * ### API仕様
     * 1. 下記条件のデータを取得
     *    * テーブル《t_comparison_insert_headers》
     *      * t_comparison_insert_headers.lp_order_id = lpOrderId
     *      * t_comparison_insert_headers.deleted_at is null
     *      * t_comparison_insert_headers.sort_order 昇順で並べ替え
     *    * テーブル《t_comparison_insert_items》
     *      * リレーション先《t_comparison_insert_headers》
     *        * t_comparison_insert_items.comparison_header_id = t_comparison_insert_headers.id
     *      * t_comparison_insert_items.sort_order でグループ化し昇順で並べ替え
     *      * t_comparison_insert_items.t_comparison_insert_headers.sort_order 昇順で並べ替え
     *      * editable => false
     * 1. 抽出結果を返す
     *
     * @urlParam lpOrderId integer required LP構成ID Example:1
     * @responseFile status=200 storage/responses/ComparisonInsert.json
     * @responseFile status=500 storage/responses/http_status_cd_500.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($lpOrderId,ShowService $service)
    {
        $data = $service->exec($lpOrderId);
        return $this->setResponse($data);
    }

    /**
     * 他社比較入力テーブル情報の初期登録(機能ID：F-00005-00006)
     *
     * ### API仕様
     *
     * ### やりたいこと
     * * 《t_companies_comparison_headers》《t_comparison_insert_items》テーブルの初期登録
     *
     * ### リレーション
     * * t_companies_comparison_headers
     *   * t_comparison_insert_items（1対Nでcomparison_header_idでリレーション）
     *
     * ### 処理詳細
     * #### トランザクション制御
     *   * 関連テーブルへの全操作を1トランザクションとして扱う。
     *   * どこかで失敗（エラー）になった際はロールバック。
     *   * 問題なく、全DB操作が完了した際はコミット。
     *
     * #### 初期登録処理
     *   * ヘッダーの初期登録
     *     *「店舗名」「商品名」「URL」のみの登録
     *   * テーブル情報の初期登録
     *     * 初期登録として3行分空文字を新規登録。
     *
     * @response {
     *   "dataArray": "",
     *   "normalMessage": "更新完了しました。",
     *   "errorMessage": "",
     * }
     * @responseFile status=500 storage/responses/http_status_cd_500.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(ComparisonInsertCreateRequest $request,CreateService $service)
    {
        $lp_order_id = $request->lp_order_id;
        $headers = [
            ["header_name"=>"同じ仕様・BM", "header_type"=>"text","comparison_insert_flag"=>1,"companies_comparison_flag"=>0],
            ["header_name"=>"店舗名", "header_type"=>"text","comparison_insert_flag"=>1,"companies_comparison_flag"=>1],
            ["header_name"=>"商品名", "header_type"=>"text","comparison_insert_flag"=>1,"companies_comparison_flag"=>1],
            ["header_name"=>"URL", "header_type"=>"url","comparison_insert_flag"=>1,"companies_comparison_flag"=>1],
            ["header_name"=>"商品画像", "header_type"=>"image","comparison_insert_flag"=>1,"companies_comparison_flag"=>1],
            ["header_name"=>"FV", "header_type"=>"image","comparison_insert_flag"=>0,"companies_comparison_flag"=>1],
            ["header_name"=>"価格", "header_type"=>"text","comparison_insert_flag"=>1,"companies_comparison_flag"=>0],
            ["header_name"=>"内容量or何日分", "header_type"=>"text","comparison_insert_flag"=>1,"companies_comparison_flag"=>0],
            ["header_name"=>"g単価 or 1日あたり", "header_type"=>"calculation","comparison_insert_flag"=>1,"companies_comparison_flag"=>0],
            ["header_name"=>"仕様など", "header_type"=>"text","comparison_insert_flag"=>1,"companies_comparison_flag"=>0],
            ["header_name"=>"成分", "header_type"=>"text","comparison_insert_flag"=>1,"companies_comparison_flag"=>0],
            ["header_name"=>"その他", "header_type"=>"text","comparison_insert_flag"=>1,"companies_comparison_flag"=>0],
            ["header_name"=>"レビュー数・評価", "header_type"=>"image","comparison_insert_flag"=>1,"companies_comparison_flag"=>0],
            ["header_name"=>"客層(男性)　※画像", "header_type"=>"image","comparison_insert_flag"=>1,"companies_comparison_flag"=>0],
            ["header_name"=>"客層(女性)　※画像", "header_type"=>"image","comparison_insert_flag"=>1,"companies_comparison_flag"=>0],
            ["header_name"=>"購入理由", "header_type"=>"text","comparison_insert_flag"=>1,"companies_comparison_flag"=>0],
            ["header_name"=>"良いレビュー", "header_type"=>"text","comparison_insert_flag"=>1,"companies_comparison_flag"=>0],
            ["header_name"=>"悪いレビュー", "header_type"=>"text","comparison_insert_flag"=>1,"companies_comparison_flag"=>0],
        ];
        $service->exec(compact("lp_order_id","headers"));
        return $this->setResponse('', CommonMsg::MSG_ID_000001, '');
    }

    /**
     * 他社比較入力テーブル情報の編集(機能ID：F-00005-00007)
     *
     * ### API仕様
     *
     * ### やりたいこと
     * * t_companies_comparison_headers,t_companies_comparison_itemsテーブルへ情報の登録・更新・削除
     *
     * ### リレーション
     * * t_companies_comparison_headers
     *   * t_companies_comparison_items（1対Nでcompanies_header_idでリレーション）
     *
     * ### リクエストパラメータとテーブルのマッピング
     * * headers ⇒ t_companies_comparison_headers
     *   * t_companies_comparison_headers.sort_order => headers[]のインデックス番号
     * * items ⇒ t_companies_comparison_items
     *   * t_companies_comparison_items.col => items[]のインデックス番号
     *
     * ### 処理詳細
     *
     * #### トランザクション制御
     * * 関連テーブルへの全操作を1トランザクションとして扱う。
     *   * どこかで失敗（エラー）になった際はロールバック。
     *   * 問題なく、全DB操作が完了した際はコミット。
     *
     * #### 《t_companies_comparison_headers》《t_companies_comparison_items》への操作
     * * リクエストパラメータ、テーブルともに同一IDのデータが存在する場合 ⇒ 更新処理
     *   * マッピング情報を元に更新処理
     * * リクエストパラメータのIDが空の場合 ⇒ 登録処理
     *   * マッピング情報を元に登録処理
     * * itesm[].file が存在する場合(更新、登録どちらでも行う)
     *   * 画像の保存処理を行う
     *     * パス=>/storage/lp_order/{{lpOrderId}}/ComparisonInsert/{{t_comparison_insert_items.id}}.jpg
     *   * t_comparison_insert_items.text に上記パスをセット
     * * テーブルにIDが存在するがリクエストパラメータにそのIDが含まれていない（存在しない）場合 ⇒ 削除処理
     *   * 対象レコードを論理削除
     *
     * @response {
     *   "dataArray": "",
     *   "normalMessage": "更新完了しました。",
     *   "errorMessage": "",
     * }
     * @urlParam lpOrderId integer required LP構成ID Example:1
     * @responseFile status=500 storage/responses/http_status_cd_500.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ComparisonInsertUpdateRequest $request,$lpOrderId,UpdateService $service)
    {
        $service->exec($request,$lpOrderId);
        return $this->setResponse('', CommonMsg::MSG_ID_000002, '');
    }

    /**
     * 他社比較入力テーブル情報を他構成からコピーする(機能ID：F-00005-00013)
     *
     * ### API仕様
     * 1. DBトランザクション開始
     * 1. コピー先の既存データ削除処理（※既存データとコピーされたデータを混在させないための処置）
     *    * リクエストパラメータ[lp_order_id]に紐づく下記テーブルのデータを論理削除
     *      * テーブル:《t_comparison_insert_headers》
     *      * テーブル:《t_comparison_insert_items》
     * 1. 下記条件のデータを取得
     *    * テーブル《t_comparison_insert_headers》
     *      * t_comparison_insert_headers.lp_order_id = other_lp_order_id
     *    * テーブル《t_comparison_insert_items》
     *      * t_comparison_insert_items.id = t_comparison_insert_items.comparison_header_id
     * 1. 《t_comparison_insert_headers》の登録処理
     *    * リクエストパラメータ[lp_order_id]を[lp_order_id]へセット
     *    * 上記で取得したデータをオブジェクトへセット
     *    * 登録処理
     * 1. 《t_comparison_insert_items》の登録処理
     *    * 《t_comparison_insert_headers》の最新IDを各オブジェクトの[basic_knowledge_id]へセット
     *    * 上記で取得したデータを各オブジェクトへセット
     *    * 登録処理
     * 1. 成功時
     *    * コミット
     *    * setResponseのノーマルメッセージにCommonMsg::MSG_ID_000001をセットして返す。
     * 1. 失敗時
     *    * ロールバック
     *    * 例外をthrow
     *    * setResponseのエラーメッセージにCommonMsg::MSG_ID_000101をセットして返す。
     * 1. 抽出結果を返す
     *
     * @responseFile status=500 storage/responses/http_status_cd_500.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function copy(ComparisonInsertCopyRequest $request,CopyService $service)
    {
        $service->exec($request);
        return $this->setResponse('', CommonMsg::MSG_ID_000001, '');
    }

}
