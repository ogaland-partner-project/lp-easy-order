<?php

namespace Database\Seeds;

use Illuminate\Database\Seeder;
use App\Models\TConstitutionProcess;
use App\Models\TLpOrder;

class TConstitutionProcessTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $row_1 = TLpOrder::where("product_name", "サンプル１")->first();
        $row_2 = TLpOrder::where("product_name", "サンプル２")->first();
        $model = TConstitutionProcess::create([
            'lp_order_id' => $row_1['id'],
            'concept_word' => 'コンセプト01',
            'concept_catch' => 'キャッチ01',
            'how_block' => 'ブロック内容01',
        ]);
        $model = TConstitutionProcess::create([
            'lp_order_id' => $row_2['id'],
            'concept_word' => 'コンセプト02',
            'concept_catch' => 'キャッチ02',
            'how_block' => 'ブロック内容02',
        ]);

    }
}
