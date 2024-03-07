<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LpOrderCopyRequest extends FormRequest
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
            "other_lp_order_id" => ""
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
            "lp_order_id" => [ "description" => "LP構成ID", "example" => "" ],
            "other_lp_order_id" => [ "description" => "LP構成ID", "example" => 1 ],
        ];
    }
}