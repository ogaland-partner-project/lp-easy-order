<?php

namespace App\Http\Controllers\API;

use App\Services\SampleService;
use App\Services\BaseServices\DbCrud;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SampleController extends Controller
{
    //サービスClas格納変数
    private $_service;


    /**
     * コンストラクタ
     */
    public function __construct()
    {
        //サービスClasのインスタンス化
        $this->_service = new SampleService();
        //サービス基底クラスを直接インスタンス化してもＯＫ
        //$this->_service = new DbCrud("orders");
    }
    /**
     * テーブルの対象データ一覧取得（ページ単位）
     *
     * @param Request $request
     * @return string JSON形式のデータ 
     */
    public function GetDataAll(Request $post)
    {

        //サービスのメソッドを通じて戻り値を取得
        $json = $this->_service->GetDataAll($post);

        return $json;
    }

    /**
     * データ保存（id: 数値の場合は更新、空文字は追加）
     *
     * @param Request $post 更新パラメータ
     * @return string JSON形式のデータ 
     */
    public function SaveData(Request $post)
    {

        $json = $this->_service->SaveData($post);

        return $json;
    }

    /**
     * データ削除
     *
     * @param Request $post
     * @return string JSON形式のデータ 
     */
    public function DeleteData(Request $post)
    {

        $json = $this->_service->DeleteData($post);

        return $json;
    }

    /**
     * データ取得（１件）
     *
     * @param [type] $id 注文者ID
     * @return void
     */
    public function GetDataOnce($id)
    {
        $json = $this->_service->GetDataOnce($id);

        return $json;
    }

    // /**
    //  * Undocumented function
    //  *
    //  * @return void
    //  */
    // public function logout()
    // {

    //     // api_tokenをnullにする
    //     $user = Auth::user();
    //     $user->update(['api_token' => null]);

    //     Auth::logout();

    //     return response()->json(['ststud' => 'ok']);
    // }
}
