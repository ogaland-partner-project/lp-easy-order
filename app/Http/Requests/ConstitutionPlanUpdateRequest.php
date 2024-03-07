<?php

namespace App\Http\Requests;

use Faker\Core\Color;

class ConstitutionPlanUpdateRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
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
            "constitution_plan" => [ "description" => "LPブロック内容", "example" => [
                [
                    "id"=>1,
                    "block_detail"=>"店舗名2",
                    "requester_fix"=>"店舗名を修正してください",
                    "pharmaceutical_affairs_fix"=>"店舗名をチェックしてください",
                    "information_management_memo"=>"店舗名を管理します",
                ],
                [
                    "id"=>2,
                    "block_detail"=>"構成2",
                    "requester_fix"=>"構成を修正してください",
                    "pharmaceutical_affairs_fix"=>"構成をチェックしてください",
                    "information_management_memo"=>"構成を管理します",
                ],
            ]
        ],
        "image_paths" => [ "description" => "画像", "example" => [
                [
                    [
                        "id"=>1,
                        "extension"=>"",
                        "images_key"=>"",
                    ],
                ],
                [
                    [
                        "id"=>3,
                        "extension"=>"",
                        "images_key"=>"",
                    ],
                    [
                        "id"=>null,
                        "extension"=>"jpg",
                        "images_key"=>"0",
                    ],
                ]
            ]
        ],
        "memos" => [ "description" => "画像メモ", "example" => [
                [
                    [
                        "id"=>1,
                        "memo"=>"みつぎのメモ",
                        "category"=>"小",
                    ],
                ],
                [
                    [
                        "id"=>2,
                        "memo"=>"バッグのめも",
                        "category"=>"中",
                    ],
                    [
                        "id"=>3,
                        "memo"=>"テスト",
                        "category"=>"テスト",
                    ]
                ]
            ]
        ],
        ];
    }
}
