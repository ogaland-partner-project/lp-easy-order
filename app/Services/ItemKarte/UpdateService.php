<?php

namespace App\Services\ItemKarte;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Common\CommonMsg;
use App\Exceptions\AppException;
use App\Models\TItemKarte;
use App\Models\TRawMaterial;

class UpdateService
{
    /**
     * 商品カルテ情報更新
     *
     * @param int $itemKarteId
     * @param array $param
     */
    public function execUpdate($itemKarteId, $param)
    {
        try {
            DB::beginTransaction();

            $itemKarte = TItemKarte::find($itemKarteId);
            if (!$itemKarte) {
                throw new AppException(CommonMsg::MSG_ID_000006);
            }

            $row = $param;
            unset($row['material_list']);
            $row = array_merge($row, [
                'created_pg' => 'ItemKarte.CreateService.execCreate',
                'updated_pg' => 'ItemKarte.CreateService.execCreate',
            ]);
            $itemKarte->fill($row)->save();

            // パラメータに含まれない原材料情報は削除
            $ids = collect($param['material_list'])->filter(function ($row) {
                return isset($row['id']);
            })->map(function ($row) {
                return $row['id'];
            });
            $itemKarte->rawMaterials->each(function ($rawMaterial) use ($ids) {
                if (!$ids->contains($rawMaterial->id)) {
                    $rawMaterial->delete();
                }
            });

            collect($param['material_list'])->each(function ($row) use ($itemKarte) {
                // idが指定されている場合は更新、そうでなければ登録
                $rawMaterial = null;
                if (isset($row['id']) && !is_null($row['id'])) {
                    $rawMaterial = TRawMaterial::find($row['id']);
                    if (!$rawMaterial) {
                        throw new AppException(CommonMsg::MSG_ID_000812 . $row['id']);
                    }
                    if ($rawMaterial->item_karte_id !== $itemKarte->id) {
                        throw new AppException(CommonMsg::MSG_ID_000813 . $row['id']);
                    }
                } else {
                    $rawMaterial = new TRawMaterial();
                    $row = array_merge($row, [
                        'item_karte_id' => $itemKarte->id,
                        'created_pg' => 'ItemKarte.UpdateService.execUpdate',
                    ]);
                }
                $row['updated_pg'] = 'ItemKarte.UpdateService.execUpdate';
                $rawMaterial->fill($row)->save();
            });

            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }
}
