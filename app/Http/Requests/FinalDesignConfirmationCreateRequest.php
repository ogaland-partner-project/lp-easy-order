<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinalDesignConfirmationCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "design_parts.*.lp_order_id" => "required|integer",
            "design_parts.*.image_path" => "",
            "design_parts.*.file" => "",
            "design_parts.*.design_memo" => "",
            "design_parts.*.law_support_memo" => "",
            "design_parts.*.info_manage_memo" => "",
            "design_parts.*.sort_order" => ""
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
            "design_parts" => [ "description" => "デザインパーツ","example" => [
                    [
                        "lp_order_id" => 2,
                        "image_path" => "",
                        "file" => "origenal.jpg",
                        "design_memo" => "<p>ここにHTML記法で修正必要内容が入る</p>",
                        "law_support_memo" => "<p>ここにHTML記法で修正必要内容が入る</p>",
                        "info_manage_memo" =>"<p>ここにHTML記法で再修正・メモが入る</p>",
                        "sort_order" => 1
                    ],
                ]
            ],
        ];
    }
}
