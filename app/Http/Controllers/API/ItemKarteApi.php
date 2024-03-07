<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\ItemKarteCreateRequest;
use App\Http\Requests\ItemKarteUpdateRequest;
use App\Http\Requests\ItemKarteCopyRequest;
use App\Http\Resources\ItemKarteDetailsResource;
use App\Common\CommonMsg;
use App\Services\ItemKarte\ShowService;
use App\Services\ItemKarte\CreateService;
use App\Services\ItemKarte\UpdateService;
use App\Services\ItemKarte\CopyService;

/**
 * 商品カルテ入力のAPIクラス
 * @group ItemKarteApi
 */
class ItemKarteApi extends ApiController
{
    /**
     * 商品カルテの各項目を検索(機能ID：F-00003-00001)
     *
     * ### API仕様
     *
     * 1. 下記条件で商品カルテの情報を取得
     *    * テーブル《t_item_kartes》
     *      * t_item_kartes.lp_order_id = [lpOrderId]
     *      * t_item_kartes.deleted_at IS NULL
     *    * リレーション先テーブル《t_raw_materials》も含む
     *        * t_item_kartes.id = t_raw_materials.item_karte_id
     *        * ORDER BY t_raw_materials.id ASC
     *
     * 1. 取得結果を返す
     *
     * @urlParam lpOrderId integer required LP構成ID Example:1
     * @responseFile status=200 storage/responses/ItemKarte.json
     * @responseFile status=500 storage/responses/http_status_cd_500.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($lpOrderId, ShowService $service)
    {
        $data = $service->execShow($lpOrderId);
        return $this->setResponse($data, '', '');
    }

    /**
     * 商品カルテの各項目を新規登録(機能ID：F-00003-00002)
     *
     * ### API仕様
     *
     * 1. DBトランザクション開始
     *
     * 1. 登録用のオブジェクト生成
     *    * テーブル:《t_item_kartes》
     *    * テーブル:《t_raw_materials》
     *
     * 1. 上記オブジェクトに登録データをセット
     *    * 登録データ：リクエストパラメータの値
     *
     * 1. 登録処理
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
    public function create(ItemKarteCreateRequest $request, CreateService $service)
    {
        $service->execCreate($request->all());
        return $this->setResponse('', CommonMsg::MSG_ID_000001, '');
    }

    /**
     * 商品カルテの各項目を更新(機能ID：F-00003-00003)
     *
     * ### API仕様
     *
     * 1. DBトランザクション開始
     *
     * 1. 下記条件により更新対象データを取得
     *    * テーブル《t_item_kartes》
     *      * t_item_kartes.lp_order_id = [lpOrderId]
     *      * t_item_kartes.deleted_at IS NULL
     *    * リレーション先テーブル《t_raw_materials》も含む
     *    - 上記条件でデータが取得できなかった場合、例外とする
     *      - エラーメッセージにCommonMsg::MSG_ID_000006をセットして、例外をthrowする
     *
     * 1. 《t_item_kartes》の更新処理
     *      * Request bodyパラメータを元に更新処理
     *
     * 1. 《t_raw_materials》への操作
     *      * Request bodyパラメータの[material_list.id]（※以降（イ））と、<br>
     *        更新対象データのt_raw_materials.id（※以降（ロ））の状態を比較判断して処理を行う。
     *          * （イ）が（ロ）に存在する場合
     *              * 更新処理 ⇒ （イ）に属する各パラメーターの値で（ロ）を更新
     *          * （イ）が空の場合
     *              * 登録処理 ⇒ （イ）に属する各パラメーターの値を（ロ）に追加
     *          * （ロ）が（イ）に存在しない場合
     *              * 削除処理 ⇒ （ロ）のdeleted_atに処理日付を設定し更新
     *
     * 1. 成功時
     *    * コミット
     *    * setResponseのノーマルメッセージにCommonMsg::MSG_ID_000002をセットして返す。
     *
     * 1. 失敗時
     *    * ロールバック
     *    * 例外をthrow
     *    * setResponseのエラーメッセージにCommonMsg::MSG_ID_000102をセットして返す。
     *
     * @urlParam itemKarteId integer required 商品カルテID Example:1
     * @response {
     *   "dataArray": "",
     *   "normalMessage": "編集完了しました。",
     *   "errorMessage": "",
     * }
     * @responseFile status=500 storage/responses/http_status_cd_500.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($itemKarteId, ItemKarteUpdateRequest $request, UpdateService $service)
    {
        $service->execUpdate($itemKarteId, $request->all());
        return $this->setResponse('', CommonMsg::MSG_ID_000002, '');
    }

    /**
     * 商品カルテを他構成からのコピー作成(機能ID：F-00003-00005)
     *
     * ### API仕様
     *
     * 1. DBトランザクション開始
     *
     * 1. コピー先の既存データ削除処理（※既存データとコピーされたデータを混在させないための処置）
     *    * リクエストパラメータ[lpOrderId]に紐づく下記テーブルのデータを論理削除
     *      * テーブル:《t_item_kartes》
     *      * テーブル:《t_raw_materials》
     *
     * 1. 下記条件でコピー元データを取得
     *    * テーブル《t_item_kartes》
     *      * t_item_kartes.lp_order_id = [other_lp_order_id]
     *    * テーブル《t_raw_materials》
     *      * t_item_kartes.id = t_raw_materials.item_karte_id
     *    - 上記条件でデータが取得できなかった場合、例外とする
     *      - エラーメッセージにCommonMsg::MSG_ID_000006をセットして、例外をthrowする
     *
     * 1. 《t_item_kartes》の登録処理
     *    * リクエストパラメータ[lpOrderId]を[lp_order_id]へセット
     *    * 上記で取得したコピー元データをオブジェクトへセット
     *    * 登録処理
     *
     * 1. 《t_raw_materials》の登録処理
     *    * 《t_item_kartes》の最新IDを《t_raw_materials》オブジェクトの[item_karte_id]へセット
     *    * 上記で取得したコピー元データを《t_raw_materials》オブジェクトへセット
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
    public function copy(ItemKarteCopyRequest $request, CopyService $service)
    {
        $service->execCopy($request->all());
        return $this->setResponse('', CommonMsg::MSG_ID_000001, '');
    }
}
