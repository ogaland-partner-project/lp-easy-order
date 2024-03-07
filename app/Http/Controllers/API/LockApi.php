<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Services\Lock\CreateService;
use App\Services\Lock\ShowService;
use App\Services\Lock\DeleteService;
use App\Services\Lock\UnlockService;

class LockApi extends ApiController
{

    /**
     * 編集ロックをかけるAPI
     *
     * * ロックをかける構成のIDとメニューのIDをロックテーブルへ挿入
     *
     * @param CreateService $service
     * @param Request $request
     * @return void
     */
    public function create(CreateService $service, Request $request)
    {
        $id = $service->execCreate($request);
        return $this->setResponse($id);
    }

    /**
     * 編集ロックされているかの確認API
     *
     * * 開いている画面の構成、メニューのIDがロックテーブルに存在しているかを確認
     * * 存在していたら、画面側で編集できないようにする
     *
     * @param ShowService $service
     * @param Request $request
     * @return void
     */
    public function show(ShowService $service, Request $request)
    {
        $lock = $service->show($request);
        if ($lock) {
            return $this->setResponse($lock, '他ユーザーがこの画面を編集中です');
        }
        return $this->setResponse($lock);
    }

    /**
     * 編集ロック解除API
     *
     * * ロックテーブルの対象レコードを物理削除する
     *
     * @param DeleteService $service
     * @param [type] $id
     * @return void
     */
    public function delete(DeleteService $service, Request $request)
    {
        $service->delete($request->data);
    }

    /**
     * 強制的に編集ロックを解除するAPI
     *
     * * 何らかの理由で編集していないのに、ロック情報のみが残ってしまった際の応急処置
     * * 画面側から選択した構成のロック情報を全て削除する
     *
     * @param UnlockService $service
     * @param [type] $lp_order_id
     * @return void
     */
    public function unlock(UnlockService $service, $lp_order_id)
    {
        $service->unlock($lp_order_id);
    }
}
