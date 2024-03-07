<?php

namespace App\Http\Requests;

class LpOrderUpdateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "product_name" => "string",
            "product_code" => "string",
            "description" => "string",
            "status" => "integer",
            "requirement_flag" => "integer",
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
            "product_name" => [ "description" => "構成名", "example" => "ミックスナッツ2" ],
            "product_code" => [ "description" => "品目コード", "example" => "MN0012" ],
            "description" => [ "description" => "簡易説明", "example" => "楽天みつぎ工作のミックスナッツ2" ],
            "status" => [ "description" => "新着情報", "example" => 1 ],
            "requirement_flag" => [ "description" => "必要フラグ", "example" => 0 ],
        ];
    }
}
