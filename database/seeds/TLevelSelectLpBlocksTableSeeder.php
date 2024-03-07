<?php

namespace Database\Seeds;

use Illuminate\Database\Seeder;
use App\Models\TLevelSelectLpBlock;
use App\Models\TLevelSelect;

class TLevelSelectLpBlockTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $row_1 = TLevelSelect::where("promoter", "サンプル花子01")->first();
        $row_2 = TLevelSelect::where("promoter", "サンプル花子02")->first();

        $model = TLevelSelectLpBlock::create([
            'level_select_id' => $row_1['id'],
            'block_detail' => '商品画像を大きく目立たせる',
            'sort_order' => '1',
        ]);
        $model = TLevelSelectLpBlock::create([
            'level_select_id' => $row_1['id'],
            'block_detail' => '商品の細かい仕様をわかりやすく',
            'sort_order' => '2',
        ]);
        $model = TLevelSelectLpBlock::create([
            'level_select_id' => $row_2['id'],
            'block_detail' => '商品説明を詳細に',
            'sort_order' => '1',
        ]);
        $model = TLevelSelectLpBlock::create([
            'level_select_id' => $row_2['id'],
            'block_detail' => '色彩を分かりやすく',
            'sort_order' => '2',
        ]);
    }

}
