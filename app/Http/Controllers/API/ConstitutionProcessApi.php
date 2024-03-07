<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\ConstitutionProcessCreateRequest;
use App\Http\Requests\ConstitutionProcessUpdateRequest;
use App\Http\Requests\ConstitutionProcessCopyRequest;
use App\Models\TConstitutionProcess;
use App\Models\TConstitutionBlock;
use App\Models\TConstitutionFixBlock;
use App\Services\ConstitutionProcess\ShowService;
use App\Http\Resources\ConstitutionProcessResource;
use App\Services\ConstitutionProcess\CreateService;
use App\Services\ConstitutionProcess\UpdateService;
use App\Services\ConstitutionProcess\CopyService;
use App\Common\CommonMsg;
use Symfony\Component\HttpFoundation\Request;
/**
 * 構成の手順のAPIクラス
 * @group ConstitutionProcessApi
 */
class ConstitutionProcessApi extends ApiController
{
    /**
     * 構成の手順の各項目を検索(機能ID：F-00007-00001)
     *
     * ### API仕様
     *
     * 1. 下記条件で構成の手順のデータを取得
     *    * テーブル《t_constitution_processes》
     *      * t_constitution_processes.lp_order_id = [lpOrderId]
     *      * t_constitution_processes.deleted_at IS NULL
     *      * リレーション先《t_constitution_blocks》も含む
     *        * t_constitution_processes.id = t_constitution_blocks.constitution_process_id
     *        * ORDER BY t_constitution_blocks.sort_order ASC
     *      * リレーション先《t_constitution_fix_blocks》も含む
     *        * t_constitution_processes.id = t_constitution_fix_blocks.constitution_process_id
     *        * ORDER BY t_constitution_fix_blocks.sort_order ASC
     *
     * 1. 取得結果を返す
     *
     * @urlParam lpOrderId integer required LP構成ID Example:1
     * @responseFile status=200 storage/responses/ConstitutionProcess.json
     * @responseFile status=500 storage/responses/http_status_cd_500.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ShowService $service, $lpOrderId)
    {
        $datas = $service->findById($lpOrderId);
        return $this->setResponse($datas);
    }

    /**
     * 構成の手順の各項目を新規登録(機能ID：F-00007-00002)
     *
     * ### API仕様
     *
     * 1. DBトランザクション開始
     *
     * 1. 登録用のオブジェクト生成
     *    * テーブル:《t_constitution_processes》
     *    * テーブル:《t_constitution_blocks》
     *    * テーブル:《t_constitution_fix_blocks》
     *
     * 1. 上記オブジェクトに登録データをセット
     *    * 登録データ：リクエストパラメータの値
     *
     * 1. 登録処理
     *
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
    public function create(CreateService $service, ConstitutionProcessCreateRequest $request)
    {
        $service->insert($request);
        return $this->setResponse([], CommonMsg::MSG_ID_000001);
    }

    /**
     * 構成の手順の各項目を更新(機能ID：F-00007-00003)
     *
     * ### API仕様
     *
     * 1. DBトランザクション開始
     *
     * 1. 下記条件により更新対象データを取得
     *    * テーブル《t_constitution_processes》
     *      * t_constitution_processes.lp_order_id = [lpOrderId]
     *      * t_constitution_processes.deleted_at IS NULL
     *    * リレーション先テーブル《t_constitution_blocks》も含む
     *    * リレーション先テーブル《t_constitution_fix_blocks》も含む
     *    - 上記条件でデータが取得できなかった場合、例外とする
     *      - エラーメッセージにCommonMsg::MSG_ID_000006をセットして、例外をthrowする
     *
     * 1. 《t_constitution_processes》の更新処理
     *      * Request bodyパラメータを元に更新処理
     *
     * 1. 《t_constitution_blocks》への操作
     *      * Request bodyパラメータの[constitution_blocks_list.id]（※以降（イ））と、<br>
     *        更新対象データのt_constitution_blocks.id（※以降（ロ））の状態を比較判断して処理を行う。
     *          * （イ）が（ロ）に存在する場合
     *              * 更新処理 ⇒ （イ）に属する各パラメーターの値で（ロ）を更新
     *          * （イ）が空の場合
     *              * 登録処理 ⇒ （イ）に属する各パラメーターの値を（ロ）に追加
     *          * （ロ）が（イ）に存在しない場合
     *              * 削除処理 ⇒ （ロ）のdeleted_atに処理日付を設定し更新
     *
     * 1. 《t_constitution_fix_blocks》への操作
     *      * Request bodyパラメータの[constitution_fix_block_list.id]（※以降（ハ））と、<br>
     *        更新対象データのt_constitution_fix_blocks.id（※以降（ニ））の状態を比較判断して処理を行う。
     *          * （ハ）が（ニ）に存在する場合
     *              * 更新処理 ⇒ （ハ）に属する各パラメーターの値で（ニ）を更新
     *          * （ハ）が空の場合
     *              * 登録処理 ⇒ （ハ）に属する各パラメーターの値を（ニ）に追加
     *          * （ニ）が（ハ）に存在しない場合
     *              * 削除処理 ⇒ （ニ）のdeleted_atに処理日付を設定し更新
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
    public function update($lpOrderId, UpdateService $service, ConstitutionProcessUpdateRequest $request)
    {
        $service->update($lpOrderId, $request);
        return $this->setResponse([], CommonMsg::MSG_ID_000002);
    }

    /**
     * 構成の手順を他構成からのコピー作成(機能ID：F-00007-00005)
     *
     * ### API仕様
     *
     * 1. DBトランザクション開始
     *
     * 1. コピー先の既存データ削除処理（※既存データとコピーされたデータを混在させないための処置）
     *    * リクエストパラメータ[lpOrderId]に紐づく下記テーブルのデータを論理削除
     *      * テーブル:《t_constitution_processes》
     *      * テーブル:《t_constitution_blocks》
     *      * テーブル:《t_constitution_fix_blocks》
     *
     * 1. 下記条件でコピー元データを取得
     *    * テーブル《t_constitution_processes》
     *      * t_constitution_processes.lp_order_id = [other_lp_order_id]
     *    * テーブル《t_constitution_blocks》
     *      * t_constitution_processes.id = t_constitution_blocks.constitution_process_id
     *    * テーブル《t_constitution_fix_blocks》
     *      * t_constitution_processes.id = t_constitution_fix_blocks.constitution_process_id
     *    - 上記条件でデータが取得できなかった場合、例外とする
     *      - エラーメッセージにCommonMsg::MSG_ID_000006をセットして、例外をthrowする
     *
     * 1. 《t_constitution_processes》の登録処理
     *    * リクエストパラメータ[lpOrderId]を[lp_order_id]へセット
     *    * 上記で取得したコピー元データをオブジェクトへセット
     *    * 登録処理
     *
     * 1. 《t_constitution_blocks》の登録処理
     *    * 《t_constitution_processes》の最新IDを《t_constitution_blocks》オブジェクトの[constitution_process_id]へセット
     *    * 上記で取得したコピー元データを《t_constitution_blocks》オブジェクトへセット
     *    * 登録処理
     *
     * 1. 《t_constitution_fix_blocks》の登録処理
     *    * 《t_constitution_processes》の最新IDを《t_constitution_fix_blocks》オブジェクトの[constitution_process_id]へセット
     *    * 上記で取得したコピー元データを《t_constitution_fix_blocks》オブジェクトへセット
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
    public function copy(CopyService $service, ConstitutionProcessCopyRequest $request)
    {
        $service->exec($request);
        return $this->setResponse([], CommonMsg::MSG_ID_000001);
    }
}
