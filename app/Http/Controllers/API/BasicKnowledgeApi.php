<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Requests\BasicKnowledgeCreateRequest;
use App\Http\Requests\BasicKnowledgeUpdateRequest;
use App\Http\Requests\BasicKnowledgeCopyRequest;
use App\Common\CommonMsg;
use App\Services\BasicKnowledge\ShowService;
use App\Services\BasicKnowledge\CreateService;
use App\Services\BasicKnowledge\UpdateService;
use App\Services\BasicKnowledge\CopyService;

/**
 * 基礎知識画面のAPIクラス
 * @group BasicKnowledgeApi
 */
class BasicKnowledgeApi extends ApiController
{
    /**
     * 基礎知識画面の各項目を検索(機能ID：F-00004-00001)
     *
     * ### API仕様
     * 1. 下記条件のデータを取得
     *    * テーブル《t_basic_knowledges》
     *      * t_basic_knowledges.lp_order_id = lpOrderId
     *    * テーブル《t_basic_knowledge_details》
     *      * t_basic_knowledge_details.basic_knowledge_id = t_basic_knowledges.id
     *    * テーブル《t_basic_knowledge_images》
     *      * t_basic_knowledge_images.basic_knowledge_id = t_basic_knowledges.id
     *    * テーブル《t_basic_knowledge_urls》
     *      * t_basic_knowledge_urls.basic_knowledge_id = t_basic_knowledges.id
     * 1. 《t_basic_knowledge_details》のデータ整形
     *    * [col]でグループ化
     *    * 上記の配列を[col]の昇順に並べ替える
     *    * 上記配列内の配列を[sort_order]の昇順に並べ替える
     * 1. 抽出結果を返す
     *
     * @urlParam lpOrderId integer required LP構成ID Example:1
     * @responseFile status=200 storage/responses/BasicKnowledge.json
     * @responseFile status=500 storage/responses/http_status_cd_500.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($lpOrderId, ShowService $service)
    {
        $data = $service->execShow($lpOrderId);
        return $this->setResponse($data, '', '');
    }

    /**
     * 基礎知識画面の各項目を新規登録(機能ID：F-00004-00002)
     *
     * ### API仕様
     * ### やりたいこと
     * * t_basic_knowledgesテーブルの関連テーブルへ情報の登録
     * ### リレーション
     * * t_basic_knowledges
     *   * t_basic_knowledge_details（1対Nでbasic_knowledge_idでリレーション）
     *   * t_basic_knowledge_images（1対Nでbasic_knowledge_idでリレーション）
     *   * t_basic_knowledge_urls（1対Nでbasic_knowledge_idでリレーション）
     * ### リクエストパラメータとテーブルのマッピング
     * * details ⇒ t_basic_knowledge_details
     *   * t_basic_knowledge_details.col => details[]のインデックス番号
     *   * t_basic_knowledge_details.sort_order => details[][]のインデックス番号
     * * images ⇒ t_basic_knowledge_images
     *   * t_basic_knowledge_images.image_path => /storage/lp_order/{{lp_order_id}}/basicknowledge/{{t_basic_knowledge_images.id}}.jpg
     *   * └{{xxxx}}⇒ここは該当IDに置き換えて処理する
     * * urls ⇒ t_basic_knowledge_urls
     * * 上記以外 ⇒ t_basic_knowledges
     *
     * ### 処理詳細
     *
     * #### トランザクション制御
     * * 関連テーブルへの全操作を1トランザクションとして扱う。
     *   * どこかで失敗（エラー）になった際はロールバック。
     *   * 問題なく、全DB操作が完了した際はコミット。
     *
     * #### t_basic_knowledgesへの操作
     * * マッピング情報を元に登録処理
     *
     * #### t_basic_knowledge_imagesへの操作
     * * images[].file にファイルが入力されていた場合は、マッピング情報のパスを参考にして画像の保存処理
     * * マッピング情報を元に登録処理
     *
     * #### t_basic_knowledge_urlsへの操作
     * * マッピング情報を元に登録処理
     *
     * @response {
     *   "dataArray": "",
     *   "normalMessage": "登録完了しました。",
     *   "errorMessage": "",
     * }
     * @responseFile status=500 storage/responses/http_status_cd_500.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(BasicKnowledgeCreateRequest $request, CreateService $service)
    {
        $service->execCreate($request->all());
        return $this->setResponse('', CommonMsg::MSG_ID_000001, '');
    }

    /**
     * 基礎知識画面の各項目を更新(機能ID：F-00004-00003)
     *
     * ### API仕様
     * ### やりたいこと
     * * t_basic_knowledgesテーブルの関連テーブルへ情報の登録・更新・削除
     * ### リレーション
     * * t_basic_knowledges
     *   * t_basic_knowledge_details（1対Nでbasic_knowledge_idでリレーション）
     *   * t_basic_knowledge_images（1対Nでbasic_knowledge_idでリレーション）
     *   * t_basic_knowledge_urls（1対Nでbasic_knowledge_idでリレーション）
     * ### リクエストパラメータとテーブルのマッピング
     * * details ⇒ t_basic_knowledge_details
     *   * t_basic_knowledge_details.col => details[]のインデックス番号
     *   * t_basic_knowledge_details.sort_order => details[][]のインデックス番号
     * * images ⇒ t_basic_knowledge_images
     *   * t_basic_knowledge_images.image_path => /storage/lp_order/{{lpOrderId}}/basicknowledge/{{t_basic_knowledge_images.id}}.jpg
     *   * └{{xxxx}}⇒ここは該当IDに置き換えて処理する
     * * urls ⇒ t_basic_knowledge_urls
     * * 上記以外 ⇒ t_basic_knowledges
     *
     * ### 処理詳細
     *
     * #### トランザクション制御
     * * 関連テーブルへの全操作を1トランザクションとして扱う。
     *   * どこかで失敗（エラー）になった際はロールバック。
     *   * 問題なく、全DB操作が完了した際はコミット。
     *
     * #### t_basic_knowledgesへの操作
     * * マッピング情報を元に更新処理
     *
     * #### t_basic_knowledge_details,t_basic_knowledge_images,t_basic_knowledge_urlsへの操作
     * * リクエストパラメータ、テーブルともに同一IDのデータが存在する場合 ⇒ 更新処理
     *   * マッピング情報を元に更新処理
     * * リクエストパラメータのIDが空の場合 ⇒ 登録処理
     *   * マッピング情報を元に登録処理
     * * テーブルにIDが存在するがリクエストパラメータにそのIDが含まれていない（存在しない）場合 ⇒ 削除処理
     *   * 対象レコードを論理削除
     * * images[].file にファイルが入力されている場合は、マッピング情報のパスを参考にして画像の保存処理
     *   * t_basic_knowledge_images.image_pathへ保存先のパスをセットして更新
     *
     * @response {
     *   "dataArray": "",
     *   "normalMessage": "更新完了しました。",
     *   "errorMessage": "",
     * }
     * @urlParam basicKnowledgeId integer required LP構成ID Example:1
     * @responseFile status=500 storage/responses/http_status_cd_500.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($basicKnowledgeId, BasicKnowledgeUpdateRequest $request, UpdateService $service)
    {
        $service->execUpdate($basicKnowledgeId, $request->all());
        return $this->setResponse('', CommonMsg::MSG_ID_000002, '');
    }

    /**
     * 基礎知識画面の各項目を他構成からコピーする(機能ID：F-00004-00008)
     *
     * ### API仕様
     *
     * 1. DBトランザクション開始
     *
     * 1. コピー先の既存データ削除処理（※既存データとコピーされたデータを混在させないための処置）
     *    * リクエストパラメータ[lp_order_id]に紐づく下記テーブルのデータを論理削除
     *      * テーブル:《t_basic_knowledges》
     *      * テーブル:《t_basic_knowledge_details》
     *      * テーブル:《t_basic_knowledge_images》
     *      * テーブル:《t_basic_knowledge_urls》
     *
     * 1. 下記条件のデータを取得
     *    * テーブル《t_basic_knowledges》
     *      * t_basic_knowledges.lp_order_id = other_lp_order_id
     *    * テーブル《t_basic_knowledge_details》
     *      * t_basic_knowledges.id = t_basic_knowledge_details.basic_knowledge_id
     *    * テーブル《t_basic_knowledge_images》
     *      * t_basic_knowledges.id = t_basic_knowledge_images.basic_knowledge_id
     *    * テーブル《t_basic_knowledge_urls》
     *      * t_basic_knowledges.id = t_basic_knowledge_urls.basic_knowledge_id
     *    - 上記条件でデータが取得できなかった場合、例外とする
     *         - エラーメッセージにCommonMsg::MSG_ID_000006をセットして、例外をthrowする
     *
     * 1. 《t_basic_knowledges》の登録処理
     *    * リクエストパラメータ[lp_order_id]を[lp_order_id]へセット
     *    * 上記で取得したデータをオブジェクトへセット
     *    * 登録処理
     * 1. 各オブジェクトの登録処理
     *    * 《t_basic_knowledges》の最新IDを各オブジェクトの[basic_knowledge_id]へセット
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
    public function copy(BasicKnowledgeCopyRequest $request, CopyService $service)
    {
        $service->execCopy($request->all());
        return $this->setResponse('', CommonMsg::MSG_ID_000001, '');
    }
}
