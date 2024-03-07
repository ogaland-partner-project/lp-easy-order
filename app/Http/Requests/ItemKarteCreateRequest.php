<?php

namespace App\Http\Requests;

class ItemKarteCreateRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "lp_order_id" => "required|integer",
            "goods_name" => "",
            "trademark_flag" => "integer",
            "chinese_characters" => "",
            "kana" => "",
            "romaji" => "",
            "material_list.*.raw_material_name" => "",
            "material_list.*.production_area" => "",
            "material_list.*.raw_material_detail" => "",
            "material_list.*.production_area_publish_flag" => "integer",
            "material_list.*.photo_material_flag" => "integer",
            "material_list.*.certificate_flag" => "integer",
            "material_list.*.coverage_content_flag" => "integer",
            "material_list.*.document_path" => "",
            "goods_specifications" => "",
            "price_including_tax" => "integer",
            "jas_mark" => "",
            "jas_mark_folder" => "",
            "jas_mark_certification" => "",
            "target_jendar" => "",
            "target_age" => "string",
            "target_statue" => "",
            "BM_goods_name1" => "",
            "BM_url1" => "",
            "BM_goods_name2" => "",
            "BM_url2" => "",
            "difference_point" => "",
            "concept" => "",
            "supplier_information_sharing" => "",
            "strong_point" => "",
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
            "lp_order_id" => [ "description" => "LP構成ID", "example" => 1 ],
            "goods_name" => [ "description" => "正式な商品名", "example" => "10種のミックスナッツ" ],
            "trademark_flag" => [ "description" => "商品登録可能性", "example" => 1 ],
            "chinese_characters" => [ "description" => "漢字", "example" => "10種のミックスナッツ" ],
            "kana" => [ "description" => "カナ", "example" => "10シュノミックスナッツ" ],
            "romaji" => [ "description" => "ローマ字", "example" => "10shunomikkusunattsu" ],
            "material_list.*.raw_material_name" => [ "description" => "原材料", "example" => "アーモンド" ],
            "material_list.*.production_area" => [ "description" => "産地", "example" => "アメリカ" ],
            "material_list.*.raw_material_detail" => [ "description" => "原料(成分)の詳細・強み", "example" => "大きい" ],
            "material_list.*.production_area_publish_flag" => [ "description" => "産地掲載の有無", "example" => 1 ],
            "material_list.*.photo_material_flag" => [ "description" => "写真素材の有無", "example" => 1 ],
            "material_list.*.certificate_flag" => [ "description" => "証明書関係の有無", "example" => 1 ],
            "material_list.*.coverage_content_flag" => [ "description" => "取材内容の有無", "example" => 1 ],
            "material_list.*.document_path" => [ "description" => "資料保管場所", "example" => "storage/documents/Almond.doc" ],
            "goods_specifications" => [ "description" => "商品仕様", "example" => "商品仕様：10粒入り" ],
            "price_including_tax" => [ "description" => "税込販売価格", "example" => 1980 ],
            "jas_mark" => [ "description" => "JASマーク", "example" => "" ],
            "jas_mark_folder" => [ "description" => "JASマークデータフォルダ", "example" => "storage/jas/JasAlmond.svg" ],
            "jas_mark_certification" => [ "description" => "JASマーク認定(都道府県)", "example" => "鹿児島県" ],
            "target_jendar" => [ "description" => "ターゲット性別", "example" => "男性" ],
            "target_age" => [ "description" => "ターゲット年齢層", "example" => "30" ],
            "target_statue" => [ "description" => "ターゲット人間層", "example" => "参考値" ],
            "BM_goods_name1" => [ "description" => "BM商品名", "example" => "10種のミックスナッツ" ],
            "BM_url1" => [ "description" => "BM URL", "example" => "http://bm_url1_1.com/Almond" ],
            "BM_goods_name2" => [ "description" => "BM商品名", "example" => "ミックスナッツ" ],
            "BM_url2" => [ "description" => "BM URL", "example" => "http://bm_url1_2.com/Almond" ],
            "difference_point" => [ "description" => "差別化ポイント", "example" => "参考値" ],
            "concept" => [ "description" => "コンセプト", "example" => "安くたくさん" ],
            "supplier_information_sharing" => [ "description" => "業者情報共有欄", "example" => "価格が1年を通して安定しています。" ],
            "strong_point" => [ "description" => "強み", "example" => "安定感" ],

        ];
    }
}
