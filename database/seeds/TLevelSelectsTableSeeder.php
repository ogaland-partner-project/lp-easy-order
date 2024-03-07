<?php

namespace Database\Seeds;

use Illuminate\Database\Seeder;
use App\Models\TLevelSelect;
use App\Models\TLpOrder;

class TLevelSelectTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $row_1 = TLpOrder::where("product_name", "サンプル１")->first();
        $row_2 = TLpOrder:where("product_name", "サンプル２")->first();

        $model = TLevelSelect::create([
            'lp_order_id' => $row_1['id'],
            'promoter' => 'サンプル花子01',
            'configurator' => 'サンプル太郎01',
            'designer' => 'サンプル花美01',
            'level' => '1',
            'purpose' => 'ユニバーサルデザイン',
            'point1' => '清潔感',
            'point2' => 'おしゃれ',
            'point3' => 'すばらしい',
            'taste' => 'みたいなデザイン',
        ]);
        $model = TLevelSelect::create([
            'lp_order_id' => $row_2['id'],
            'promoter' => 'サンプル花子02',
            'configurator' => 'サンプル太郎02',
            'designer' => 'サンプル花美02',
            'level' => '2',
            'purpose' => 'スタイリッシュデザイン',
            'point1' => '精錬された',
            'point2' => 'スタイリッシュ',
            'point3' => '鮮やかな',
            'taste' => 'ようなデザイン',
        ]);
    }

}
