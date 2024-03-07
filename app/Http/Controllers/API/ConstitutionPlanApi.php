<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ConstitutionPlanCreateRequest;
use App\Http\Requests\ConstitutionPlanUpdateRequest;
use App\Http\Requests\ConstitutionPlanCopyRequest;
use App\Services\ConstitutionPlan\ShowService;
use App\Services\ConstitutionPlan\CreateService;
use App\Services\ConstitutionPlan\UpdateService;
use App\Services\ConstitutionPlan\CopyService;
use App\Common\CommonMsg;

/**
 * 構成案画面のAPIクラス
 * @group ConstitutionPlanApi
 */
class ConstitutionPlanApi extends ApiController
{
    /**
     * 構成案情報の検索(機能ID：F-00008-00002)
     *
     * ### API仕様
     * 1. 下記条件のデータを取得
     *    * テーブル《t_constitution_plans》
     *      * t_constitution_plans.lp_order_id = lpOrderId
     *      * t_constitution_plans.deleted_at is null
     *      * t_constitution_plans.sort_order 昇順で並べ替え
     *    * テーブル《t_plan_images》
     *      * リレーション先《t_constitution_plans》
     *        * t_constitution_plans.constitution_plan_id = t_constitution_plans.id
     *      * t_plan_images.constitution_plan_id でグループ化
     *      * t_plan_images.sort_order 昇順で並べ替え
     *    * テーブル《t_plan_image_memos》
     *      * リレーション先《t_plan_images》
     *        * t_plan_image_memos.plan_image_id = t_plan_images.id
     *      * t_plan_image_memos.sort_order 昇順で並べ替え
     * 1. 上記抽出結果をresponsesを参考に整形
     *    * images => t_plan_images.constitution_plan_id でグループ化
     *    * memos => t_plan_image_memos.constitution_plan_id でグループ化
     * 1. 抽出結果を返す
     *
     * @urlParam lpOrderId integer required LP構成ID Example:1
     * @responseFile status=200 storage/responses/ConstitutionPlan.json
     * @responseFile status=500 storage/responses/http_status_cd_500.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($lpOrderId,ShowService $service)
    {
        $data = $service->exec($lpOrderId);
        return $this->setResponse($data);
    }

    /**
     * 構成案画像の各項目新規登録(機能ID：F-00008-00003)
     *
     * ### API仕様
     * ### やりたいこと
     * * 《t_constitution_plans》《t_plan_images》《t_plan_image_memos》テーブルへ情報の登録
     * ### リレーション
     * * t_constitution_plans
     *   * t_plan_images（1対Nでconstitution_plan_idでリレーション）
     *   * t_plan_image_memos（1対Nでconstitution_plan_idでリレーション）
     * ### リクエストパラメータとテーブルのマッピング
     * * constitution_plan ⇒ t_constitution_plans
     *   * t_constitution_plans.sort_order => constitution_plan[]のインデックス番号
     * * images ⇒ t_plan_images
     *   * t_plan_images.constitution_plan_id => 同じ階層(インデックス番号が同じ)にあるconstitution_plan[]のid
     *   * t_plan_images.sort_order => images[]のインデックス番号
     *   * t_plan_images.image_path => /storage/lp_order/{{lp_order_id}}/constitutionPlan/{{t_plan_images.id}}.jpg
     *   * └{{xxxx}}⇒ここは該当IDに置き換えて処理する
     * * memos ⇒ t_plan_image_memos
     *   * t_plan_image_memos.constitution_plan_id => 同じ階層(インデックス番号が同じ)にあるconstitution_plan[]のid
     *   * t_plan_image_memos.sort_order => memos[]のインデックス番号
     * ### 処理詳細
     * #### トランザクション制御
     *   * 関連テーブルへの全操作を1トランザクションとして扱う。
     *   * どこかで失敗（エラー）になった際はロールバック。
     *   * 問題なく、全DB操作が完了した際はコミット。
     * #### リクエストパラメータの値を《t_constitution_plans》《t_plan_images》《t_plan_image_memos》に登録（対象分繰り返す）
     *   * マッピング情報を元にリクエストパラメータの値をオブジェクトにセット
     *   * images[].file が存在する場合
     *     * 画像の保存処理を行う
     *       * パス=>/storage/lp_order/{{lp_order_id}}/constitutionPlan/{{t_plan_images.id}}.jpg
     *
     * @response {
     *   "dataArray": "",
     *   "normalMessage": "更新完了しました。",
     *   "errorMessage": "",
     * }
     * @responseFile status=500 storage/responses/http_status_cd_500.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(ConstitutionPlanCreateRequest $request, CreateService $service)
    {
        $service->exec($request);
    }

    /**
     * 構成案画面の各項目編集(機能ID：F-00008-00004)
     *
     * ### API仕様
     * ### やりたいこと
     * * 《t_constitution_plans》《t_plan_images》《t_plan_image_memos》テーブルへ情報の登録,更新,削除
     * ### リレーション
     * * t_constitution_plans
     *   * t_plan_images（1対Nでconstitution_plan_idでリレーション）
     *   * t_plan_image_memos（1対Nでconstitution_plan_idでリレーション）
     * ### リクエストパラメータとテーブルのマッピング
     * * constitution_plan ⇒ t_constitution_plans
     *   * t_constitution_plans.sort_order => constitution_plan[]のインデックス番号
     * * images ⇒ t_plan_images
     *   * t_plan_images.constitution_plan_id => 同じ階層(インデックス番号が同じ)にあるconstitution_plan[]のid
     *   * t_plan_images.sort_order => images[]のインデックス番号
     *   * t_plan_images.image_path => /storage/lp_order/{{lpOrderId}}/constitutionPlan/{{t_plan_images.id}}.jpg
     *   * └{{xxxx}}⇒ここは該当IDに置き換えて処理する
     * * memos ⇒ t_plan_image_memos
     *   * t_plan_images.constitution_plan_id => 同じ階層(インデックス番号が同じ)にあるconstitution_plan[]のid
     *   * t_plan_image_memos.sort_order => memos[]のインデックス番号
     * ### 処理詳細
     * #### トランザクション制御
     * * 関連テーブルへの全操作を1トランザクションとして扱う。
     *   * どこかで失敗（エラー）になった際はロールバック。
     *   * 問題なく、全DB操作が完了した際はコミット。
     * #### 《t_constitution_plans》《t_plan_images》《t_plan_image_memos》への操作（対象分繰り返す）
     * * リクエストパラメータ、テーブルともに同一IDのデータが存在する場合 ⇒ 更新処理
     *   * マッピング情報を元に更新処理
     * * リクエストパラメータのIDが空の場合 ⇒ 登録処理
     *   * マッピング情報を元に登録処理
     * * テーブルにIDが存在するがリクエストパラメータにそのIDが含まれていない（存在しない）場合 ⇒ 削除処理
     *   * 対象レコードを論理削除
     * * images[].file が存在する場合
     *   * 画像の保存処理を行う
     *     * パス=>/storage/lp_order/{{lpOrderId}}/constitutionPlan/{{t_plan_images.id}}.jpg
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
    public function update($lpOrderId,ConstitutionPlanUpdateRequest $request,UpdateService $service)
    {
        $service->exec($lpOrderId,$request);
        return $this->setResponse([], CommonMsg::MSG_ID_000002);
    }

    /**
     * 構成案画像の各項目を他構成からコピーする(機能ID：F-00008-00008)
     *
     * ### API仕様
     * 1. DBトランザクション開始
     * 1. コピー先の既存データ削除処理（※既存データとコピーされたデータを混在させないための処置）
     *    * リクエストパラメータ[lpOrderId]に紐づく下記テーブルのデータを論理削除
     *      * テーブル:《t_constitution_plans》
     *      * テーブル:《t_plan_images》
     *      * テーブル:《t_plan_image_memos》
     *
     * 1. 下記条件のデータを取得
     *    * テーブル《t_constitution_plans》
     *      * t_constitution_plans.lp_order_id = other_lp_order_id
     *    * テーブル《t_plan_images》
     *      * t_plan_images.id = t_constitution_plans.constitution_plan_id
     *    * テーブル《t_plan_image_memos》
     *      * t_plan_image_memos.id = t_constitution_plans.constitution_plan_id
     *    - 上記条件でデータが取得できなかった場合、例外とする
     *         - エラーメッセージにCommonMsg::MSG_ID_000006をセットして、例外をthrowする
     *
     * 1. 《t_constitution_plans》の登録処理
     *    * リクエストパラメータ[lpOrderId]を[lp_order_id]へセット
     *    * 上記で取得したデータをオブジェクトへセット
     *    * 登録処理
     * 1. 《t_plan_images》《t_plan_image_memos》の登録処理
     *    * 《t_constitution_plans》の最新IDを各オブジェクトの[constitution_plan_id]へセット
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
    public function copy(ConstitutionPlanCopyRequest $request,CopyService $service)
    {
        $service = new CopyService();
        $service->exec($request);
    }

}
