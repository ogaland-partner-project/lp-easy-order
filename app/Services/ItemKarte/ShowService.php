<?php

namespace App\Services\ItemKarte;

use App\Models\TItemKarte;

class ShowService
{
    /**
     * 商品カルテ情報取得
     *
     * @param int $lpOrderId
     */
    public function execShow($lpOrderId)
    {
        $itemKartes = TItemKarte::where('lp_order_id', $lpOrderId)
            ->orderBy('id')
            ->get();

        $data = $itemKartes->map(function ($itemKarte) {
            $material_list = $itemKarte->rawMaterials->sortBy('id')->map(function ($rawMaterial) {
                return [
                    'id' => $rawMaterial->id,
                    'item_karte_id' => $rawMaterial->item_karte_id,
                    'raw_material_name' => $rawMaterial->raw_material_name,
                ];
            })->toArray();
            if(empty($material_list)){
                $material_list = [[
                    'id' => null,
                    'item_karte_id' => null,
                    'production_area' => '',
                ]];
            }
            return [
                'id' => $itemKarte->id,
                'lp_order_id' => $itemKarte->lp_order_id,
                'goods_name' => $itemKarte->goods_name,
                'goods_specifications' => $itemKarte->goods_specifications,
                'price_including_tax' => $itemKarte->price_including_tax,
                'concept' => $itemKarte->concept,
                'target_jendar' => $itemKarte->target_jendar,
                'target_age' => $itemKarte->target_age,
                'target_statue' => $itemKarte->target_statue,
                'BM_goods_name1' => $itemKarte->BM_goods_name1,
                'BM_url1' => $itemKarte->BM_url1,
                'difference_point' => $itemKarte->difference_point,
                'strong_point' => $itemKarte->strong_point,
                'memo' => $itemKarte->memo,
                'material_list' => $material_list
            ];
        })->toArray();

        return $data;
    }
}
