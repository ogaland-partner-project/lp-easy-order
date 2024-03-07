<?php

namespace App\Http\Requests;

class BasicKnowledgeUpdateRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
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
            "question" => [ "description" => "疑問点", "example" => "ミックスナッツ市場はどうなのか" ],
            "others" => [ "description" => "他社で掲載している証明書など", "example" => "特になし" ],
            "details" => [ "description" => "基礎知識内容","example" => [
                    [
                        ["id"=>1, "title"=>"商品/成分名", "text"=>"ミックスナッツ"],
                        ["id"=>2, "title"=>"一般的な認知度", "text"=>"高い"]
                    ],
                    [
                        ["id"=>4, "title"=>"商品/成分名", "text"=>"ピーナッツ"],
                        ["id"=>5, "title"=>"一般的な認知度", "text"=>"最高"]
                    ],
                    [
                        ["id"=>null, "title"=>"商品/成分名", "text"=>"マカダミアナッツ"],
                        ["id"=>null, "title"=>"一般的な認知度", "text"=>"中"]
                    ]
                ]
            ],
            "images" => [ "description" => "基礎知識画像", "example" => [
                    [
                        "id"=>1,
                        "image_path"=>"/storage/lp_order/1/BasicKnowledge/1.jpg",
                        "file"=>"",
                        "memo"=>"ミックスナッツの画像です～"
                    ],
                    [
                        "id"=>2,
                        "image_path"=>"/storage/lp_order/1/BasicKnowledge/2.jpg",
                        "image"=>"",
                        "memo"=>"ピーナッツの画像です"
                    ],
                    [
                        "id"=>null,
                        "image_path"=>"",
                        "file"=>"maka.jpg",
                        "memo"=>"マカダミアナッツの画像です～"
                    ],
                ]
            ],
            "urls" => [ "description" => "url", "example" => [
                    ["id"=>1,"url"=>"https://~~~~"],
                    ["id"=>3,"url"=>"https://~~~~"],
                    ["id"=>null,"url"=>"https://~~~~"]
                ]
            ],
        ];
    }
}
