<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Support\Arr;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * リクエストクラスの基底クラス
 * - バリデーション定義は子クラスで定義
 */
abstract class BaseRequest extends FormRequest
{
    /** バリデーション定義 */
    abstract function rules();

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
     * バリデーション失敗時のjson生成（共通）
     *
     * @param Validator $validator
     * @return void
     */
    protected function failedValidation(Validator $validator) {
        $res = response()->json([
            "dataArray"     => [],
            "normalMessage" => "",
            "errorMessage"  => implode(",", Arr::flatten($validator->errors()->toArray())) // エラーメッセージ
        ], 422);

        throw new HttpResponseException($res);
    }
}