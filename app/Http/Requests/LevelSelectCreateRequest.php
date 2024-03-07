<?php

namespace App\Http\Requests;


class LevelSelectCreateRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "lp_order_id" => "",
            "promoter" => "",
            "configurator" => "",
            "designer" => "",
            "level" => "",
            "purpose" => "",
            "point1" => "",
            "point2" => "",
            "point3" => "",
            "taste" => "",
            "t_level_select_lp_blocks" => "",
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
            "Promoter" => [ "description" => "販促担当者", "example" => "尾賀太郎" ],
            "configurator" => [ "description" => "構成担当者", "example" => "尾賀次郎" ],
            "designer" => [ "description" => "デザイン担当者", "example" => "尾賀三郎" ],
            "level" => [ "description" => "レベル 1~4", "example" => 2 ],
            "purpose" => [ "description" => "訴求内容、目的", "example" => "誰でも分かりやすいデザイン" ],
            "point1" => [ "description" => "ポイント1", "example" => "清潔感のある" ],
            "point2" => [ "description" => "ポイント2", "example" => "おしゃれな" ],
            "point3" => [ "description" => "ポイント3", "example" => "すばらしい" ],
            "taste" => [ "description" => "デザイン・テイスト", "example" => "～みたいなデザイン" ],
            "lp_block" => [ "description" => "構成ブロック", "example" => [["id"=>0, "text"=>"商品画像を大きく目立させる"],["id"=>0, "text"=>"商品の細かい仕様をわかりやすく"]] ],
        ];
    }
}
