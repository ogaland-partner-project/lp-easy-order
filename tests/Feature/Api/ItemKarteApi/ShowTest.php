<?php

namespace Tests\Feature\Api\ItemKarteApi;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\TLpOrder;
use App\Models\TItemKarte;
use App\Models\TRawMaterial;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    const API_URL = '/api/lp_easy_order/item_karte/';

    /**
     * 共通初期処理
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // t_lp_ordersテーブル
        collect([
            [
                'id' => 1001,
                'status' => 0,
                'requirement_flag' => 0,
            ],
            [
                'id' => 1002,
                'status' => 1,
                'requirement_flag' => 1,
            ],
        ])->each(function ($row) {
            factory(TLpOrder::class)->create(
                array_merge($row, [
                    'product_name' => '構成名_' . $row['id'],
                    'product_code' => 'CD_' . $row['id'],
                    'description' => '簡易説明_' . $row['id'],
                ])
            );
        });

        // t_item_kartesテーブル
        collect([
            [
                'id' => 100101,
                'lp_order_id' => 1001,
                'trademark_flag' => 0,
                'price_including_tax' => 10000,
                'target_jendar' => '男性',
                'target_age' => 10,
            ],
            [
                'id' => 100102,
                'lp_order_id' => 1001,
                'trademark_flag' => 1,
                'price_including_tax' => 20000,
                'target_jendar' => '女性',
                'target_age' => 20,
            ],
            [
                'id' => 100103,
                'lp_order_id' => 1001,
                'trademark_flag' => 1,
                'price_including_tax' => 30000,
                'target_jendar' => '男性',
                'target_age' => 30,
                'deleted_at' => now(),      // 削除
            ],
            [
                'id' => 100201,
                'lp_order_id' => 1002,
                'trademark_flag' => 0,
                'price_including_tax' => 10000,
                'target_jendar' => '男性',
                'target_age' => 10,
            ],
        ])->each(function ($row) {
            factory(TItemKarte::class)->create(
                array_merge($row, [
                    'goods_name' => '商品名_' . $row['id'],
                    'chinese_characters' => '漢字_' . $row['id'],
                    'kana' => 'カナ_' . $row['id'],
                    'romaji' => 'ローマ字_' . $row['id'],
                    'goods_specifications' => '商品仕様_' . $row['id'],
                    'jas_mark' => 'J' . $row['id'],
                    'jas_mark_folder' => 'storage/jas/' . $row['id'] . '.svg',
                    'jas_mark_certification' => 'JASマーク認定_'  . $row['id'],
                    'target_statue' => 'ターゲット層_'  . $row['id'],
                    'BM_goods_name1' => 'BM商品名1_'  . $row['id'],
                    'BM_url1' => 'https://url1.com/'  . $row['id'],
                    'BM_goods_name2' => 'BM商品名2_'  . $row['id'],
                    'BM_url2' => 'https://url2.com/'  . $row['id'],
                    'difference_point' => '差別化ポイント_'  . $row['id'],
                    'concept' => 'コンセプト_'  . $row['id'],
                    'supplier_information_sharing' => '業者情報共有欄_' . $row['id'],
                    'strong_point' => '強み_'  . $row['id'],    
                ])
            );
        });

        // t_raw_materialsテーブル
        $rawMaterials = collect([
            [
                'id' => 10010101,
                'item_karte_id' => 100101,
                'production_area_publish_flag' => 0,
                'photo_material_flag' => 0,
                'certificate_flag' => 0,
                'coverage_content_flag' => 0,
            ],
            [
                'id' => 10010102,
                'item_karte_id' => 100101,
                'production_area_publish_flag' => 1,
                'photo_material_flag' => 1,
                'certificate_flag' => 1,
                'coverage_content_flag' => 1,
            ],
            [
                'id' => 10010109,
                'item_karte_id' => 100101,
                'production_area_publish_flag' => 0,
                'photo_material_flag' => 0,
                'certificate_flag' => 0,
                'coverage_content_flag' => 0,
                'deleted_at' => now(),      // 削除済
            ],
            [
                'id' => 10010201,
                'item_karte_id' => 100102,
                'production_area_publish_flag' => 0,
                'photo_material_flag' => 1,
                'certificate_flag' => 0,
                'coverage_content_flag' => 1,
            ],
            [
                'id' => 10020101,
                'item_karte_id' => 100201,
                'production_area_publish_flag' => 1,
                'photo_material_flag' => 0,
                'certificate_flag' => 1,
                'coverage_content_flag' => 0,
            ],
        ])->map(function ($row) {
            return factory(TRawMaterial::class)->create(
                array_merge($row, [
                    'raw_material_name' => '原材料_' . $row['id'],
                    'production_area' => '産地_' . $row['id'],
                    'raw_material_detail' => '詳細_' . $row['id'],
                    'document_path' => 'storage/documents/' . $row['id'] . '.doc',
                ])
            );
        });
    }

    /**
     * 共通終了処理
     *
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_正常()
    {
        $expect = [
            [
                'id' => 100101,
                'lp_order_id' => 1001,
                'goods_name' => '商品名_100101',
                'trademark_flag' => 0,
                'chinese_characters' => '漢字_100101',
                'kana' => 'カナ_100101',
                'romaji' => 'ローマ字_100101',
                'material_list' => [
                    [
                        'id' => 10010101,
                        'item_karte_id' => 100101,
                        'raw_material_name' => '原材料_10010101',
                        'production_area' => '産地_10010101',
                        'raw_material_detail' => '詳細_10010101',
                        'production_area_publish_flag' => 0,
                        'photo_material_flag' => 0,
                        'certificate_flag' => 0,
                        'coverage_content_flag' => 0,
                        'document_path' => 'storage/documents/10010101.doc',
                    ],
                    [
                        'id' => 10010102,
                        'item_karte_id' => 100101,
                        'raw_material_name' => '原材料_10010102',
                        'production_area' => '産地_10010102',
                        'raw_material_detail' => '詳細_10010102',
                        'production_area_publish_flag' => 1,
                        'photo_material_flag' => 1,
                        'certificate_flag' => 1,
                        'coverage_content_flag' => 1,
                        'document_path' => 'storage/documents/10010102.doc',
                    ],
                ],
                'goods_specifications' => '商品仕様_100101',
                'price_including_tax' => 10000,
                'jas_mark' => 'J100101',
                'jas_mark_folder' => 'storage/jas/100101.svg',
                'jas_mark_certification' => 'JASマーク認定_100101',
                'target_jendar' => '男性',
                'target_age' => 10,
                'target_statue' => 'ターゲット層_100101',
                'BM_goods_name1' => 'BM商品名1_100101',
                'BM_url1' => 'https://url1.com/100101',
                'BM_goods_name2' => 'BM商品名2_100101',
                'BM_url2' => 'https://url2.com/100101',
                'difference_point' => '差別化ポイント_100101',
                'concept' => 'コンセプト_100101',
                'supplier_information_sharing' => '業者情報共有欄_100101',
                'strong_point' => '強み_100101',
            ],
            [
                'id' => 100102,
                'lp_order_id' => 1001,
                'goods_name' => '商品名_100102',
                'trademark_flag' => 1,
                'chinese_characters' => '漢字_100102',
                'kana' => 'カナ_100102',
                'romaji' => 'ローマ字_100102',
                'material_list' => [
                    [
                        'id' => 10010201,
                        'item_karte_id' => 100102,
                        'raw_material_name' => '原材料_10010201',
                        'production_area' => '産地_10010201',
                        'raw_material_detail' => '詳細_10010201',
                        'production_area_publish_flag' => 0,
                        'photo_material_flag' => 1,
                        'certificate_flag' => 0,
                        'coverage_content_flag' => 1,
                        'document_path' => 'storage/documents/10010201.doc',
                    ],
                ],
                'goods_specifications' => '商品仕様_100102',
                'price_including_tax' => 20000,
                'jas_mark' => 'J100102',
                'jas_mark_folder' => 'storage/jas/100102.svg',
                'jas_mark_certification' => 'JASマーク認定_100102',
                'target_jendar' => '女性',
                'target_age' => 20,
                'target_statue' => 'ターゲット層_100102',
                'BM_goods_name1' => 'BM商品名1_100102',
                'BM_url1' => 'https://url1.com/100102',
                'BM_goods_name2' => 'BM商品名2_100102',
                'BM_url2' => 'https://url2.com/100102',
                'difference_point' => '差別化ポイント_100102',
                'concept' => 'コンセプト_100102',
                'supplier_information_sharing' => '業者情報共有欄_100102',
                'strong_point' => '強み_100102',
            ],
        ];

        $response = $this->json('GET', self::API_URL . 1001);
        $response->assertStatus(200)
            ->assertJson([
                'dataArray' => $expect,
                'normalMessage' => '',
                'errorMessage' => '',
            ]);
    }

    /**
     * 正常：該当レコードなし
     *
     * @return void
     */
    public function test_正常：該当レコードなし()
    {
        $response = $this->json('GET', self::API_URL . 9001);
        $response->assertStatus(200)
            ->assertJson([
                'dataArray' => [],
                'normalMessage' => '',
                'errorMessage' => '',
            ]);
    }
}
