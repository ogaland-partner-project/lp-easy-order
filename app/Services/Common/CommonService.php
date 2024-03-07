<?php

namespace App\Services\Common;

use App\Exceptions\AppException;
use Illuminate\Support\Facades\Storage;
use App\Common\CommonMsg;
use App\Models\TLpOrder;

class CommonService
{
    /**
     * ### パラメータ文字列を元に画像ファイルを保存する
     * - storage配下のAPI別ディレクトリにファイルをアップロード
     * - テーブルに参照用パスを設定
     * - （例）アップロード用パス：/public/lp_order/1/basicknowledge/2.jpg
     * - （例）参照用パス：/storage/lp_order/1/basicknowledge/2.jpg
     *
     * @param object &$imageObject      画像情報オブジェクト（必須カラム：id, lp_order_id）
     * @param int    $lpOrderId         LP構成番号
     * @param string $fileParamString   ファイルパラメータ文字列（base64でアップロードされるファイルのキー情報）
     * @param string $callingApiName    呼び出し元API名（lp_orderディレクトリ配下の格納パスに使用されます。）
     * @param string $pathSetColumn     参照用パスを設定するカラム
     * @param string $additionWord      （オプション）ファイル名付加文字（拡張子前に付加）例）1.jpg → 1_addwrd.jpg
     */
    public function saveImageFiletFromParamString(&$imageObject, $lpOrderId, $fileParamString, $callingApiName, $pathSetColumn, $additionWord = null )
    {

        // ファイルパラメータ文字列を正規表現で精査
        preg_match('/^data:image\/(\w+);base64\,(.+)$/', $fileParamString, $matches);
        if (count($matches) <= 2) {
            throw new AppException(CommonMsg::MSG_ID_000106);
        }

        // 拡張子の取得
        $ext = $matches[1];

        // アップロードファイル名取得
        $base64code = $matches[2];

        // ディレクトリ文字列取得
        $path = sprintf(
            'lp_order/%d/'.$callingApiName.'/%d'.$additionWord.'.%s',
            $lpOrderId,
            $imageObject->id,
            $ext
        );

        // ファイルのアップロード
        Storage::put('/public/'.$path, base64_decode($base64code));

        // テーブルへ参照用パスを保存
        $imageObject->$pathSetColumn = '/storage/' . $path;
        $imageObject->save();
    }

    /**
     * ### isset()と!is_nullの合体関数
     *
     *   値                |  結果
     *  ---------------------------
     *   $var = 1        |  true
     *   $var = "";      |  true
     *   $var = "0";     |  true
     *   $var = 0;       |  true
     *   $var = null;    |  false
     *   $var            |  false
     *   $var = array()  |  true
     *   $var = array(1) |  true
     *
     * @param $var 判定対象変数
     * @return $flag 判定結果
     */
    public function existsValue($var = null) {

        $flag = false;
        $flag = isset($var) && !is_null($var);

        return $flag;
    }

    /**
     * ### コピー元データ活性確認（t_lp_orders専用）
     *
     * @param array $param
     *
     * #### 説明
     * - t_lp_ordersでのみデータの活性確認を行う関数です。
     * - 各サービスで横断的に使用するため、共通関数としています。
     */
    public function copyAbleCheckLpOrder($lpOrderId){

        $isDeleted = TLpOrder::find($lpOrderId);
        if (!$isDeleted) {
            throw new AppException(CommonMsg::MSG_ID_000006);
        }

    }
}