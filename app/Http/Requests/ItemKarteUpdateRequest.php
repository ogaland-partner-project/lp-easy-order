<?php

namespace App\Http\Requests;

class ItemKarteUpdateRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "goods_name" => "",
            "goods_specifications" => "",
            "price_including_tax" => "nullable|integer",
            "material_list.*.id" => "nullable|integer",
            "material_list.*.item_karte_id" => "nullable|integer",
            "material_list.*.raw_material_name" => "",
            "concept" => "",
            "target_jendar" => "",
            "target_age" => "nullable|string",
            "target_statue" => "",
            "BM_goods_name1" => "",
            "BM_url1" => "",
            "difference_point" => "",
            "strong_point" => "",
            "memo" => "",
        ];
    }

    /**
     * リクエストパラメータの定義
     *
     * @return array
     */
    public function bodyParameters(): array
    {
        return [
            "id" => [ "description" => "カルテID", "example" => 1 ],
            "goods_name" => [ "description" => "正式な商品名", "example" => "10種のミックスナッツ" ],
            "goods_specifications" => [ "description" => "商品仕様", "example" => "商品仕様：10粒入り" ],
            "price_including_tax" => [ "description" => "税込販売価格", "example" => 1980 ],
            "material_list.*.id" => [ "description" => "素材ID", "example" => 1 ],
            "material_list.*.item_karte_id" => [ "description" => "カルテID", "example" => 1 ],
            "material_list.*.raw_material_name" => [ "description" => "原材料", "example" => "アーモンド" ],
            "concept" => [ "description" => "コンセプト", "example" => "安くたくさん" ],
            "target_jendar" => [ "description" => "ターゲット性別", "example" => "男性" ],
            "target_age" => [ "description" => "ターゲット年齢層", "example" => "30" ],
            "target_statue" => [ "description" => "ターゲット人間層", "example" => "参考値" ],
            "BM_goods_name1" => [ "description" => "BM商品名", "example" => "10種のミックスナッツ" ],
            "BM_url1" => [ "description" => "BM URL", "example" => "http://bm_url1_1.com/Almond" ],
            "difference_point" => [ "description" => "差別化ポイント", "example" => "参考値" ],
            "strong_point" => [ "description" => "強み", "example" => "安定感" ],
            "memo" => [ "description" => "メモ", "example" => "メモ" ],
        ];
    }
}
