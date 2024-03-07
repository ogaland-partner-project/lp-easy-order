<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LevelSelectCreateRequest;
use App\Http\Requests\LevelSelectUpdateRequest;
use App\Services\LevelSelect\ShowService;
use App\Services\LevelSelect\CreateService;
use App\Services\LevelSelect\UpdateService;
use App\Common\CommonMsg;

/**
 * レベル別質問事項画面のAPIクラス
 * @group LevelSelectApi
 */
class LevelSelectApi extends ApiController
{
    /**
     * レベル別質問事項画面の各項目を検索(機能ID：F-00002-00001)
     *
     * ### API仕様
     * 1. 下記条件のデータを取得
     *    * テーブル《t_level_selects》
     *      * t_level_selects.lp_order_id = lpOrderId
     *      * t_level_selects.deleted_at is null
     *    * テーブル《t_level_select_lp_blocks》
     *      * t_level_select_lp_blocks.level_select_id = t_level_selects.id
     *      * t_level_select_lp_blocks.deleted_at is null
     * 1. 抽出結果を返す
     *
     * @urlParam lpOrderId integer required LP構成ID Example:1
     * @responseFile status=200 storage/responses/LevelSelect.json
     * @responseFile status=500 storage/responses/http_status_cd_500.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ShowService $service, $lpOrderId)
    {
        $datas = $service->findById($lpOrderId);
        return $this->setResponse($datas);
    }

    /**
     * レベル別質問事項画面の各項目を新規登録(機能ID：F-00002-00003)
     *
     * ### API仕様
     * 1. DBトランザクション開始
     * 1. 《t_level_selects》の登録
     *    * 《t_level_selects》のオブジェクト作成
     *    * 各カラムに対応するリクエストパラメータの値をオブジェクトにセットし、登録する
     * 1. 《t_level_select_lp_blocks》の登録（対象分繰り返す）
     *    * 《t_level_select_lp_blocks》のオブジェクト作成
     *    * リクエストパラメータ[lp_block][text]の値を各オブジェクトにセット
     *    * リクエストパラメータ[lp_block]のindexをカラム[sort_order]にセット
     *    * 上記オブジェクトの登録処理
     * 1. 成功時
     *    * コミット
     *    * setResponseのノーマルメッセージにCommonMsg::MSG_ID_000001をセットして返す
     * 1. 失敗時
     *    * ロールバック
     *    * 例外をthrow
     *    * setResponseのエラーメッセージにCommonMsg::MSG_ID_000101をセットして返す
     *
     * @response {
     *   "dataArray": "",
     *   "normalMessage": "登録完了しました。",
     *   "errorMessage": "",
     * }
     * @responseFile status=500 storage/responses/http_status_cd_500.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(CreateService $service, LevelSelectCreateRequest $request)
    {
        $service->insert($request);
        return $this->setResponse([], CommonMsg::MSG_ID_000001);
    }

    /**
     * レベル別質問事項画面の各項目を更新(機能ID：F-00002-00004)
     *
     * ### API仕様
     * 1. DBトランザクション開始
     * 1. 《t_level_selects》の更新
     *    * urlパラメータで渡ってくる[levelSelectId]で《t_level_selects》のオブジェクトを抽出
     *    * 各カラムに対応するリクエストパラメータの値をオブジェクトにセットし、更新する
     * 1. 《t_level_select_lp_blocks》に対する処理（リクエストパラメータ[lp_block]と《t_level_select_lp_blocks》の状況に応じた処理を行う）
     *    1. リクエストパラメータ[lp_block]と、《t_level_select_lp_blocks》双方に対象のidを持つデータが存在する場合
     *       * 《t_level_select_lp_blocks》の更新処理（対象分繰り返す）
     *          * リクエストパラメータ[lp_block]の各idで《t_level_select_lp_blocks》のオブジェクトを抽出
     *          * リクエストパラメータ[lp_block][text]の値を各オブジェクトにセット
     *          * リクエストパラメータ[lp_block]のindexをカラム[sort_order]にセット
     *          * 上記オブジェクトの更新処理
     *    1. リクエストパラメータ[lp_block]のidが空のデータが存在する場合
     *       * 《t_level_select_lp_blocks》の新規登録処理（対象分繰り返す）
     *          * 新規登録用のオブジェクトを生成
     *          * リクエストパラメータ[lp_block][text]の値を各オブジェクトにセット
     *          * リクエストパラメータ[lp_block]のindexをカラム[sort_order]にセット
     *          * 上記オブジェクトの登録処理
     *    1. 《t_level_select_lp_blocks》にデータが存在するが、リクエストパラメータ[lp_block]に対象のidを持つデータが存在しない場合。
     *       * 対象レコードに論理削除処理を行う
     * 1. 成功時
     *    * コミット
     *    * setResponseのノーマルメッセージにCommonMsg::MSG_ID_000002をセットして返す
     * 1. 失敗時
     *    * ロールバック
     *    * 例外をthrow
     *    * setResponseのエラーメッセージにCommonMsg::MSG_ID_000102をセットして返す
     *
     * @urlParam lpOrderId integer required LP構成ID Example:1
     * @response {
     *   "dataArray": "",
     *   "normalMessage": "更新完了しました。",
     *   "errorMessage": "",
     * }
     * @urlParam lpOrderId integer required LP構成ID Example:1
     * @responseFile status=500 storage/responses/http_status_cd_500.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($lpOrderId, UpdateService $service, LevelSelectUpdateRequest $request)
    {
        
        $service->update($lpOrderId, $request);
        return $this->setResponse([], CommonMsg::MSG_ID_000002);
    }

}
