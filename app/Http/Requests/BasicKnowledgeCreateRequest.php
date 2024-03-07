<?php

namespace App\Http\Requests;

class BasicKnowledgeCreateRequest extends BaseRequest
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
            "details" => "array",
            "images" => "array",
            "urls" => "array",
            "question" => "",
            "others" => "",
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
            "question" => [ "description" => "疑問点", "example" => "ミックスナッツ市場はどうなのか" ],
            "others" => [ "description" => "他社で掲載している証明書など", "example" => "特になし" ],
            "details" => [ "description" => "基礎知識内容","example" => [
                    [
                        ["id"=>null, "title"=>"商品/成分名", "text"=>"ミックスナッツ"],
                        ["id"=>null, "title"=>"一般的な認知度", "text"=>"高い"]
                    ],
                    [
                        ["id"=>null, "title"=>"商品/成分名", "text"=>"ピーナッツ"],
                        ["id"=>null, "title"=>"一般的な認知度", "text"=>"最高"]
                    ]
                ]
            ],
            "images" => [ "description" => "基礎知識画像", "example" => [
                    [
                        "id"=>null,
                        "image_path"=>"",
                        "file"=>"mix.jpg",
                        "memo"=>"ミックスナッツの画像です"
                    ],
                    [
                        "id"=>null,
                        "image_path"=>"",
                        "file"=>"peanuts.jpg",
                        "memo"=>"ピーナッツの画像です"
                    ],
                ]
            ],
            "urls" => [ "description" => "url", "example" => [
                    ["id"=>null,"url"=>"https://~~~~"],
                    ["id"=>null,"url"=>"https://~~~~"],
                    ["id"=>null,"url"=>"https://~~~~"]
                ]
            ],
        ];
    }
}
