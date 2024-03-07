<?php

namespace App\Http\Requests;

class ConstitutionProcessUpdateRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "concept_word" => "",
            "constitution_catchphrase_list" => [[
                "id" => "integer",
                "catchphrase" => "",
            ]],
            "constitution_how_blocks_list" => [[
                "id" => "integer",
                "block_detail" => "",
            ]],
            "constitution_blocks_list" => [[
                "id" => "integer",
                "block_detail" => "",
            ]],
            "constitution_fix_blocks_list" => [[
                "id" => "integer",
                "block_detail" => "",
            ]],
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
            "concept_word" => [ "description" => "コンセプト名" ,"example" => "コンセプト名" ],
            "constitution_catchphrase_list" => [[
                "id" => [ "description" => "ID" ,"example" => 1 ],
                "catchphrase" => [ "description" => "ブロック内容" ,"example" => "サイズチャート" ],
            ]],
            "constitution_how_blocks_list" => [[
                "id" => [ "description" => "ID" ,"example" => 1 ],
                "block_detail" => [ "description" => "ブロック内容" ,"example" => "サイズチャート" ],
            ]],
            "constitution_blocks_list" => [[
                "id" => [ "description" => "ID" ,"example" => 1 ],
                "block_detail" => [ "description" => "ブロック内容" ,"example" => "サイズチャート" ],
            ]],
            "constitution_fix_blocks_list" => [[
                "id" =>  [ "description" => "ID" ,"example" => 1 ],
                "block_detail" => [ "description" => "ブロック内容" ,"example" => "毎日のお仕事はもちろん　結婚式や入学式にも" ],
            ]],
        ];
    }
}
