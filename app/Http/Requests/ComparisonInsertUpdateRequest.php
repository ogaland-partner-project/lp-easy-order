<?php

namespace App\Http\Requests;

use Faker\Core\Color;

class ComparisonInsertUpdateRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
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
            "headers" => [ "description" => "ヘッダー", "example" => [
                    [
                        "id"=>1,
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
            "items" => [ "description" => "テーブルの要素", "example" => [
                    [
                        ["id"=>1,"text"=>"福助 楽天市場店","color"=>"white","editable"=>false],
                        ["id"=>null,"text"=>"2090","editable"=>false],
                    ],
                    [
                        ["id"=>4,"text"=>"キュットスリムと下着通販GB-style","color"=>"white","editable"=>false],
                        ["id"=>null,"text"=>"2090","color"=>"white","editable"=>false],
                    ]
                ]
            ],
        ];
    }
}
