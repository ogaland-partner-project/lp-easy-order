<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CompaniesComparisonCreateRequest;
use App\Http\Requests\CompaniesComparisonUpdateRequest;
use App\Http\Requests\CompaniesComparisonCopyRequest;

/**
 * 他社構成比較画面のAPIクラス
 * @group CompaniesComparisonApi
 */
class CompaniesComparisonApi extends ApiController
{
    /**
     * 他社構成比較テーブル情報の検索(機能ID：F-00006-00006)
     *
     * ### API仕様
     * 1. 下記条件のデータを取得
     *    * テーブル《t_companies_comparison_headers》
     *      * t_companies_comparison_headers.lp_order_id = lpOrderId
     *      * t_companies_comparison_headers.deleted_at is null
     *      * t_companies_comparison_headers.sort_order 昇順で並べ替え
     *    * テーブル《t_companies_comparison_items》
     *      * リレーション先《t_companies_comparison_headers》
     *        * t_companies_comparison_items.comparison_header_id = t_companies_comparison_headers.id
     *      * t_companies_comparison_items.sort_order でグループ化し昇順で並べ替え
     *      * t_companies_comparison_items.t_companies_comparison_headers.sort_order 昇順で並べ替え
     *  2. 他社比較入力
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
     * @responseFile status=200 storage/responses/CompaniesComparison.json
     * @responseFile status=500 storage/responses/http_status_cd_500.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
    }

    /**
     * 他社構成比較テーブル情報を新規登録(機能ID：F-00006-00007)
     *
     * ### API仕様
     *
     * ### やりたいこと
     * * 《t_companies_comparison_headers》《t_companies_comparison_items》テーブルへ情報の登録
     *
     * ### リレーション
     * * t_companies_comparison_headers
     *   * t_companies_comparison_items（1対Nでcomparison_header_idでリレーション）
     *
     * ### リクエストパラメータとテーブルのマッピング
     * * headers ⇒ t_companies_comparison_items
     *   * t_companies_comparison_items.sort_order => headers[]のインデックス番号
     * * items ⇒ t_companies_comparison_items
     *   * t_companies_comparison_items.col => items[]のインデックス番号
     *
     * ### 処理詳細
     * #### トランザクション制御
     *   * 関連テーブルへの全操作を1トランザクションとして扱う。
     *   * どこかで失敗（エラー）になった際はロールバック。
     *   * 問題なく、全DB操作が完了した際はコミット。
     *
     * #### リクエストパラメータの値を《t_constitution_plans》《t_plan_images》《t_plan_image_memos》に登録（対象分繰り返す）
     *   * マッピング情報を元にリクエストパラメータの値をオブジェクトにセット
     *    * itesm[].file が存在する場合
     *      * 画像の保存処理を行う
     *        * パス=>/storage/lp_order/{{lp_order_id}}/ComparisonInsert/{{t_companies_comparison_items.id}}.jpg
     *        * └{{xxxx}}⇒ここは該当IDに置き換えて処理する
     *      * t_companies_comparison_items.text に上記パスをセット
     *    * 上記オブジェクトの登録処理
     *
     * @response {
     *   "dataArray": "",
     *   "normalMessage": "更新完了しました。",
     *   "errorMessage": "",
     * }
     * @responseFile status=500 storage/responses/http_status_cd_500.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(CompaniesComparisonCreateRequest $request)
    {
    }

    /**
     * 他社構成比較テーブル情報の編集(機能ID：F-00006-00008)
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
     *     * パス=>/storage/lp_order/{{lpOrderId}}/ComparisonInsert/{{t_companies_comparison_items.id}}.jpg
     *   * t_companies_comparison_items.text に上記パスをセット
     * * テーブルにIDが存在するがリクエストパラメータにそのIDが含まれていない（存在しない）場合 ⇒ 削除処理
     *   * 対象レコードを論理削除
     *
     * @urlParam lpOrderId integer required LP構成ID Example:1
     * @response {
     *   "dataArray": "",
     *   "normalMessage": "更新完了しました。",
     *   "errorMessage": "",
     * }
     * @responseFile status=500 storage/responses/http_status_cd_500.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CompaniesComparisonUpdateRequest $request)
    {
    }

    /**
     * 他社構成比較テーブルを他構成からコピーする(機能ID：F-00006-00013)
     *
     * ### API仕様
     * 1. DBトランザクション開始
     * 1. コピー先の既存データ削除処理（※既存データとコピーされたデータを混在させないための処置）
     *    * リクエストパラメータ[lp_order_id]に紐づく下記テーブルのデータを論理削除
     *      * テーブル:《t_companies_comparison_headers》
     *      * テーブル:《t_companies_comparison_items》
     * 1. 登録用のオブジェクト生成
     *    * テーブル:《t_companies_comparison_headers》
     *    * テーブル:《t_companies_comparison_items》
     * 1. 下記条件のデータを取得
     *    * テーブル《t_companies_comparison_headers》
     *      * t_companies_comparison_headers.lp_order_id = other_lp_order_id
     *    * テーブル《t_companies_comparison_items》
     *      * t_companies_comparison_items.id = t_companies_comparison_items.comparison_header_id
     * 1. 《t_companies_comparison_headers》の登録処理
     *    * リクエストパラメータ[lp_order_id]を[lp_order_id]へセット
     *    * 上記で取得したデータをオブジェクトへセット
     *    * 登録処理
     * 1. 《t_companies_comparison_items》の登録処理
     *    * 《t_companies_comparison_headers》の最新IDを各オブジェクトの[basic_knowledge_id]へセット
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
    public function copy(CompaniesComparisonCopyRequest $request)
    {
    }

}
