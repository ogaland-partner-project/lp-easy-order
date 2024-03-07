<?php

namespace App\Http\Requests;

class ConstitutionProcessCreateRequest extends BaseRequest
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
            "concept_word" => "",
            "concept_catch" => "",
            "how_block" => "",
            "constitution_blocks_list" => [[
                "constitution_process_id" => "integer",
                "block_detail" => "",
                "sort_order" => "integer",
            ]],
            "constitution_fix_blocks_list" => [[
                "constitution_process_id" => "integer",
                "block_detail" => "",
                "sort_order" => "integer",
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
            "lp_order_id" => [ "description" => "LP構成ID", "example" => 1 ],
            "concept_word" => [ "description" => "コンセプト名" ,"example" => "コンセプト名" ],
            "concept_catch" => [ "description" => "キャッチコピー" ,"example" => "キャッチコピー" ],
            "how_block" => [ "description" => "ブロック" ,"example" => 'ブロック' ],
            "constitution_blocks_list" => [[
                "constitution_process_id" => [ "description" => "構成の手順ID" ,"example" => 1 ],
                "block_detail" => [ "description" => "ブロック内容" ,"example" => "サイズチャート" ],
                "sort_order" => [ "description" => "並び順" ,"example" => 1 ],
            ]],
            "constitution_fix_blocks_list" => [[
                "constitution_process_id" =>  [ "description" => "修正後構成の手順ID" ,"example" => 1 ],
                "block_detail" => [ "description" => "ブロック内容" ,"example" => "毎日のお仕事はもちろん　結婚式や入学式にも" ],
                "sort_order" => [ "description" => "並び順" ,"example" => 1 ],
            ]],
        ];
    }
}
