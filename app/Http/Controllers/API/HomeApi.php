<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\LpOrderCreateRequest;
use App\Http\Requests\LpOrderUpdateRequest;
use App\Http\Requests\LpOrderCopyRequest;
use App\Services\Home\IndexService;
use App\Services\Home\CreateService;
use App\Services\Home\UpdateService;
use App\Services\Home\DeleteService;
use App\Services\Home\CopyService;
use App\Common\CommonMsg;

/**
 * ホーム画面のAPIクラス
 * @group HomeApi
 */
class HomeApi extends ApiController
{
    /**
     * LP構成の一覧を検索(機能ID：F-00001-00001)
     *
     * ### API仕様
     * 1. 全ての削除されていないLP構成を抽出する
     *    * 下記条件に合致するデータを取得
     *      * 《t_lp_order》の「deleted_at」がnullのデータのみ
     * 1. 抽出結果を返す
     *
     * @responseFile status=200 storage/responses/Home.json
     * @responseFile status=500 storage/responses/http_status_cd_500.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexService $service)
    {
        $datas = $service->getAll();
        return $this->setResponse($datas);
    }

    /**
     * LP構成の新規登録(機能ID：F-00001-00002)
     *
     * ### API仕様
     * 1. DBトランザクション開始
     * 1. 登録用のオブジェクト生成
     *    * テーブル:《t_lp_order》
     * 1. 上記オブジェクトに登録データをセット
     *    * 登録データ：リクエストパラメータの値
     * 1. 登録処理
     * 1. 成功時
     *    * コミット
     *    * setResponseのノーマルメッセージにCommonMsg::MSG_ID_000001をセットして返す。
     * 1. 失敗時
     *    * ロールバック
     *    * 例外をthrow
     *    * setResponseのエラーメッセージにCommonMsg::MSG_ID_000101をセットして返す。
     *
     * @response {
     *   "dataArray": "",
     *   "normalMessage": "登録完了しました。",
     *   "errorMessage": "",
     * }
     * @responseFile status=500 storage/responses/http_status_cd_500.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(CreateService $service, LpOrderCreateRequest $request)
    {
        $service->insert($request);
        return $this->setResponse([], CommonMsg::MSG_ID_000001);
    }

    /**
     * LP構成の更新(機能ID：F-00001-00003)
     *
     * ### API仕様
     * 1. DBトランザクション開始
     * 1. 更新用のオブジェクト生成
     *    * urlパラメータで渡ってくるLP構成IDで《t_lp_order》のデータを抽出
     * 1. 上記オブジェクトに更新データをセット
     *    * 更新データ：リクエストパラメータの値
     * 1. 更新処理
     * 1. 成功時
     *    * コミット
     *    * setResponseのノーマルメッセージにCommonMsg::MSG_ID_000002をセットして返す。
     * 1. 失敗時
     *    * ロールバック
     *    * 例外をthrow
     *    * setResponseのエラーメッセージにCommonMsg::MSG_ID_000102をセットして返す。
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
    public function update($lpOrderId, UpdateService $service, LpOrderUpdateRequest $request)
    {
        $service->update($lpOrderId, $request);
        return $this->setResponse([], CommonMsg::MSG_ID_000002);
    }

    /**
     * LP構成の削除(機能ID：F-00001-00004)
     *
     * ### API仕様
     * 1. DBトランザクション開始
     * 1. 削除(softdelete)用のオブジェクト生成
     *    * urlパラメータで渡ってくるLP構成IDで《t_lp_order》のデータを抽出
     * 1. idに紐づく全ての親・子・孫関係にあるテーブル全てのレコードを持つオブジェクトに対してsoftdeleteの処理
     *    * 各サービスに用意されたexecDeleteを呼び出して削除処理を行う。
     * 1. 成功時
     *    * コミット
     *    * setResponseのノーマルメッセージにCommonMsg::MSG_ID_000003をセットして返す。
     * 1. 失敗時
     *    * ロールバック
     *    * 例外をthrow
     *    * setResponseのエラーメッセージにCommonMsg::MSG_ID_000103をセットして返す。
     *
     * @urlParam lpOrderId integer required LP構成ID Example:1
     * @response {
     *   "dataArray": "",
     *   "normalMessage": "削除完了しました。",
     *   "errorMessage": "",
     * }
     * @responseFile status=500 storage/responses/http_status_cd_500.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($lpOrderId, DeleteService $service)
    {
        $service->execDelete($lpOrderId);
        return $this->setResponse([], CommonMsg::MSG_ID_000003);
    }

    /**
     * 構成コピー作成(機能ID：F-00001-00005)
     *
     * ### API仕様
     *
     * 1. DBトランザクション開始
     *
     * 1.《t_lp_order》のコピー元データ活性確認
     *    * テーブル《t_lp_order》
     *      * t_lp_order.id = [lp_order_id]
     *    - 上記条件でデータが取得できなかった場合、例外とする
     *      - エラーメッセージにCommonMsg::MSG_ID_000006をセットして、例外をthrowする
     *
     * 1.《t_lp_order》のコピー元データを新規登録
     *    * t_lp_order.product_nameの接頭に「コピー - 」を付加する。
     *    * 《t_lp_order》へ新規登録されたデータのidを取得
     *
     * 1. リクエストパラメーターのキー['lp_order_id']に新規登録で取得したidを設定
     *
     * 1. 各サービスが提供する一連のデータ複製処理を呼び出す。
     *      1. レベル別質問事項
     *      1. 商品カルテ入力
     *      1. 基礎知識
     *      1. 他社比較入力
     *      1. 他社構成比較
     *      1. 構成の手順
     *      1. 構成案
     *      1. 最終デザイン確認
     *
     * 1. 登録処理
     *    * 登録処理
     *
     * 1. 成功時
     *    * コミット
     *    * setResponseのノーマルメッセージにCommonMsg::MSG_ID_000001をセットして返す。
     *
     * 1. 失敗時
     *    * ロールバック
     *    * 例外をthrow
     *    * setResponseのエラーメッセージにCommonMsg::MSG_ID_000101をセットして返す。
     *
     * @response {
     *   "dataArray": "",
     *   "normalMessage": "登録完了しました。",
     *   "errorMessage": "",
     * }
     * @responseFile status=500 storage/responses/http_status_cd_500.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function copy(CopyService $service, LpOrderCopyRequest $request)
    {
        $service->execCopy($request->all());
        return $this->setResponse('', CommonMsg::MSG_ID_000001, '');
    }

}
