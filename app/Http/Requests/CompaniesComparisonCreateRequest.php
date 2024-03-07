<?php

namespace App\Http\Requests;

use Faker\Core\Color;

class CompaniesComparisonCreateRequest extends BaseRequest
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
            "headers" => "array",
            "items" => "array",
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
            "headers" => [ "description" => "他社構成比較テーブルのヘッダー情報", "example" => [
                    [
                        "id"=>null,
                        "header_name"=>"店舗名",
                        "header_type"=>"text",
                        "calculation_type"=>"",
                        "calculation_row"=>[],
                    ],
                    [
                        "id"=>null,
                        "header_name"=>"価格",
                        "header_type"=>"calculation",
                        "calculation_type"=>"sum",
                        "calculation_row"=>[8,9],
                    ]
                ]
            ],
            "items" => [ "description" => "他社構成比較テーブルの各要素", "example" => [
                    [
                        ["id"=>null,"text"=>"福助 楽天市場店","color"=>"white","editable"=>false],
                        ["id"=>null,"text"=>"","file"=>"XXX.jpg","color"=>"white","editable"=>false],
                    ],
                    [
                        ["id"=>null,"text"=>"キュットスリムと下着通販GB-style","color"=>"white","editable"=>false],
                        ["id"=>null,"text"=>"","file"=>"XXX.jpg","color"=>"white","editable"=>false],
                    ]
                ]
            ],
        ];
    }
}
