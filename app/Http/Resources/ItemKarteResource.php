<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemKarteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'lp_order_id' => $this->lp_order_id,
            'goods_name' => $this->goods_name,
            'trademark_flag' => $this->trademark_flag,
            'chinese_characters' => $this->chinese_characters,
            'kana' => $this->kana,
            'romaji' => $this->romaji,
            'goods_specifications' => $this->goods_specifications,
            'price_including_tax' => $this->price_including_tax,
            'jas_mark' => $this->jas_mark,
            'jas_mark_folder' => $this->jas_mark_folder,
            'jas_mark_certification' => $this->jas_mark_certification,
            'target_jendar' => $this->target_jendar,
            'target_age' => $this->target_age,
            'target_statue' => $this->target_statue,
            'BM_goods_name1' => $this->BM_goods_name1,
            'BM_url1' => $this->BM_url1,
            'BM_goods_name2' => $this->BM_goods_name2,
            'BM_url2' => $this->BM_url2,
            'difference_point' => $this->difference_point,
            'concept' => $this->concept,
            'supplier_information_sharing' => $this->supplier_information_sharing,
            'strong_point' => $this->strong_point,
            'created_pg' => $this->created_pg,
            'created_at' => $this->created_at,
            'updated_pg' => $this->updated_pg,
            'updated_at' => $this->updated_at,
            'deleted_pg' => $this->deleted_pg,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
