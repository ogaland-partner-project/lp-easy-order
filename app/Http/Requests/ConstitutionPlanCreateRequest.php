<?php

namespace App\Http\Requests;

use Faker\Core\Color;

class ConstitutionPlanCreateRequest extends BaseRequest
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
            "constitution_plan" => "array",
            "image_paths" => "array",
            "memos" => "array"
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
            "constitution_plan" => [ "description" => "LPブロック内容", "example" => [
                    [
                        "id"=>null,
                        "block_detail"=>"店舗名",
                        "requester_fix"=>"店舗名を修正してください",
                        "pharmaceutical_affairs_fix"=>"店舗名をチェックしてください",
                        "information_management_memo"=>"店舗名を管理します",
                    ],
                    [
                        "id"=>null,
                        "block_detail"=>"構成",
                        "requester_fix"=>"構成を修正してください",
                        "pharmaceutical_affairs_fix"=>"構成をチェックしてください",
                        "information_management_memo"=>"構成を管理します",
                    ],
                ]
            ],
            "image_paths" => ["description" => "画像", "example" =>[
                    [
                        [
                            "id"=>null,
                            "extension" => 'png',
                            "images_key" => 0
                        ]
                    ],
                    [
                        [
                            "id"=>null,
                            "extension" => 'png',
                            "images_key" => 1
                        ],
                        [
                            "id"=>null,
                            "extension" => 'jpg',
                            "images_key" => 2
                        ]
                    ],
                ]
            ],
            "memos" => [ "description" => "画像メモ", "example" => [
                    [
                        [
                            "id"=>null,
                            "memo"=>"みつぎのメモ",
                            "memo_category"=>"大",
                        ],
                    ],
                    [
                        [
                            "id"=>null,
                            "memo"=>"バッグのめも",
                            "memo_category"=>"中",
                        ],
                        [
                            "id"=>null,
                            "memo"=>"",
                            "memo_category"=>"小",
                        ]
                    ]
                ]
            ],
        ];
    }
}
