<?php

use Illuminate\Database\Seeder;
use App\Models\TItemKarte;

class TItemKarteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect([
            [
                'id' => 100101,
                'lp_order_id' => 1001,
                'goods_name' => '10種のミックスナッツ',
                'trademark_flag' => 1,
                'chinese_characters' => '10種のミックスナッツ',
                'kana' => '10シュノミックスナッツ',
                'romaji' => '10shunomikkusunattsu',
                'goods_specifications' => '商品仕様：10粒入り',
                'price_including_tax' => 1980,
                'jas_mark' => 'J100101',
                'jas_mark_folder' => 'storage/jas/JasAlmond.svg',
                'jas_mark_certification' => '鹿児島県',
                'target_jendar' => '男性',
                'target_age' => 30,
                'target_statue' => '中間層',
                'BM_goods_name1' => '10種のミックスナッツ',
                'BM_url1' => 'http://bm_url1_1.com/Almond',
                'BM_goods_name2' => 'ミックスナッツ',
                'BM_url2' => 'http://bm_url1_2.com/Almond',
                'difference_point' => '価格',
                'concept' => '安くたくさん',
                'supplier_information_sharing' => '価格が1年を通して安定しています。',
                'strong_point' => '安定感',
            ],
            [
                'id' => 200101,
                'lp_order_id' => 2001,
                'goods_name' => 'よもぎ茶ティーバッグ',
                'trademark_flag' => 1,
                'chinese_characters' => 'よもぎ茶ティーバッグ',
                'kana' => 'ヨモギチャティーバッグ',
                'romaji' => 'yomogichatiibaffu',
                'goods_specifications' => '商品仕様：3g×50包',
                'price_including_tax' => 1380,
                'jas_mark' => 'J200101',
                'jas_mark_folder' => 'storage/jas/JasYomogicha.svg',
                'jas_mark_certification' => '徳島県',
                'target_jendar' => '女性',
                'target_age' => 40,
                'target_statue' => '中年層',
                'BM_goods_name1' => 'よもぎ茶ティーバッグ',
                'BM_url1' => 'http://bm_url1_1.com/Yomogicha',
                'BM_goods_name2' => 'よもぎ茶',
                'BM_url2' => 'http://bm_url1_2.com/Yomogicha',
                'difference_point' => 'アロマ効果',
                'concept' => 'ダイエットにも◎',
                'supplier_information_sharing' => '残留農薬検査実施済',
                'strong_point' => '無添加',
            ],
        ])->each(function ($row) {
            $row = array_merge($row, [
                'created_pg' => 'TItemKarteSeeder',
                'updated_pg' => 'TItemKarteSeeder',
            ]);
            factory(TItemKarte::class)->create($row);
        });
    }
}
