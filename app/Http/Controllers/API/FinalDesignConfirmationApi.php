<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\FinalDesignConfirmationCreateRequest;
use App\Http\Requests\FinalDesignConfirmationUpdateRequest;
use App\Http\Requests\FinalDesignConfirmationCopyRequest;
use App\Services\FinalDesignConfirmation\ShowService;
use App\Services\FinalDesignConfirmation\CreateService;
use App\Services\FinalDesignConfirmation\CopyService;
use App\Services\FinalDesignConfirmation\UpdateService;
use App\Common\CommonMsg;
/**
 * 最終デザイン確認のAPIクラス
 * @group FinalDesignConfirmationApi
 */
class FinalDesignConfirmationApi extends ApiController
{

    /**
     * 最終デザイン確認の各項目を検索(機能ID：F-00009-00003)
     *
     * ### API仕様
     *
     * 1. 下記条件の最終デザイン確認のデータを取得
     *    * テーブル《t_final_design_confirmations》
     *      * t_final_design_confirmations.lp_order_id = [lpOrderId]
     *      * t_final_design_confirmations.deleted_at IS NULL
     *      * ORDER BY t_final_design_confirmations.sort_order ASC
     *
     * 1. 取得結果を返す
     *
     * @urlParam lpOrderId integer required LP構成ID Example:1
     * @responseFile status=200 storage/responses/FinalDesignConfirmation.json
     * @responseFile status=500 storage/responses/http_status_cd_500.json
     * @return \Illuminate\Http\JsonResponse
     */

    public function show($lpOrderId, ShowService $service)
    {
        $data = $service->execShow($lpOrderId);
        return $this->setResponse($data, '', '');
    }

    /**
     * 最終デザイン確認の各項目を新規登録(機能ID：F-00009-00004)
     *
     * ### API仕様
     *
     * 1. DBトランザクション開始
     *
     * 1. 登録用のオブジェクト生成
     *    * テーブル:《t_final_design_confirmations》
     *
     * 1. 上記オブジェクトに登録データをセット
     *    * 登録データ：リクエストパラメータの値
     *    * design_parts.fileの値が”"でない場合
     *      * 画像の登録処理を行う。
     *          * 格納パス=>/storage/lp_order/{{lp_order_id}}/finaldesignconfirmation/{{t_final_design_confirmations.id}} + "." + 画像ファイルの拡張子
     *          * t_final_design_confirmations.image_pathに上記格納パスをセット
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
    public function create(FinalDesignConfirmationCreateRequest $request, CreateService $service)
    {
        $service->execCreate($request->all());
        return $this->setResponse('', CommonMsg::MSG_ID_000001, '');
    }

    /**
     * 最終デザイン確認の各項目を更新(機能ID：F-00009-00005)
     *
     * ### API仕様
     *
     * 1. DBトランザクション開始
     *
     * 1. 下記条件により更新対象データを取得
     *    * テーブル《t_final_design_confirmations》
     *      * t_final_design_confirmations.lp_order_id = [lpOrderId]
     *      * t_final_design_confirmations.deleted_at IS NULL
     *    - 上記条件でデータが取得できなかった場合、例外とする
     *      - エラーメッセージにCommonMsg::MSG_ID_000006をセットして、例外をthrowする
     *
     * 1. 《t_final_design_confirmations》への操作
     *      * Request bodyパラメータの[id]（※以降（イ））と、<br>
     *        更新対象データのt_final_design_confirmations.id（※以降（ロ））の状態を比較判断して処理を行う。
     *          * （イ）が（ロ）に存在する場合
     *              * 更新処理 ⇒ （イ）に属する各パラメーターの値で（ロ）を更新
     *          * （イ）が空の場合
     *              * 登録処理 ⇒ （イ）に属する各パラメーターの値を（ロ）に追加
     *          * （ロ）が（イ）に存在しない場合
     *              * 削除処理 ⇒ （ロ）のdeleted_atに処理日付を設定し更新
     *      * design_parts.fileの値が”"でない場合
     *          * 画像の登録処理、または更新処理を行う。
     *              * 格納パス=>/storage/lp_order/{{lpOrderId}}/finaldesignconfirmation/{{t_final_design_confirmations.id}} + "." + 画像ファイルの拡張子
     *              * t_final_design_confirmations.image_pathに上記格納パスをセット
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
     * @urlParam lpOrderId integer required LP構成ID Example:1
     * @response {
     *   "dataArray": "",
     *   "normalMessage": "編集完了しました。",
     *   "errorMessage": "",
     * }
     * @responseFile status=500 storage/responses/http_status_cd_500.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($lpOrderId, FinalDesignConfirmationUpdateRequest $request, UpdateService $service)
    {
        $service->execUpdate($lpOrderId, $request->all());
        return $this->setResponse('', CommonMsg::MSG_ID_000002, '');
    }

    /**
     * 最終デザイン確認を他構成からのコピー作成(機能ID：F-00009-00006)
     *
     * ### API仕様
     *
     * 1. DBトランザクション開始
     *
     * 1. コピー先の既存データ削除処理（※既存データとコピーされたデータを混在させないための処置）
     *    * リクエストパラメータ[lpOrderId]に紐づく下記テーブルのデータを論理削除
     *      * テーブル:《t_final_design_confirmations》
     *
     * 1. 下記条件でコピー元データを取得
     *    * テーブル《t_final_design_confirmations》
     *      * t_final_design_confirmations.lp_order_id = [other_lp_order_id]
     *    - 上記条件でデータが取得できなかった場合、例外とする
     *      - エラーメッセージにCommonMsg::MSG_ID_000006をセットして、例外をthrowする
     *
     * 1. 《t_final_design_confirmations》の登録処理
     *    * リクエストパラメータ[lpOrderId]を[lp_order_id]へセット
     *    * 上記で取得したコピー元データをオブジェクトへセット
     *    * 登録処理
     *
     * 1. 成功時
     *    * コミット
     *    * setResponseのノーマルメッセージにCommonMsg::MSG_ID_000001をセットして返す。
     *
     * 1. 失敗時
     *    * ロールバック
     *    * 例外をthrow
     *
     *    * setResponseのエラーメッセージにCommonMsg::MSG_ID_000101をセットして返す。
     * @response {
     *   "dataArray": "",
     *   "normalMessage": "登録完了しました。",
     *   "errorMessage": "",
     * }
     * @responseFile status=500 storage/responses/http_status_cd_500.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function copy(FinalDesignConfirmationCopyRequest $request, CopyService $service)
    {
        $service->execCopy($request->all());
        return $this->setResponse('', CommonMsg::MSG_ID_000001, '');
    }
}
