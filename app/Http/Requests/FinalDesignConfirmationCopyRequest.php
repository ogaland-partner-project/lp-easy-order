<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinalDesignConfirmationCopyRequest extends FormRequest
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
            "lp_order_id" => "required|integer",
            "other_lp_order_id" => "required|integer",
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
            "other_lp_order_id" => [ "description" => "他構成のLP構成ID", "example" => 1 ],
        ];
    }
}
