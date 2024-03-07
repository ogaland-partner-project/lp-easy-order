<?php

namespace Tests\Unit\Services\ItemKarte;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Common\CommonMsg;
use Exception;
use App\Services\ItemKarte\CreateService;
use App\Models\TLpOrder;
use App\Models\TItemKarte;
use App\Models\TRawMaterial;

class CreateServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 共通初期処理
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new CreateService();

        // t_lp_ordersテーブル
        collect([
            [
                'id' => 1001,
                'status' => 0,
                'requirement_flag' => 0,
            ],
            [
                'id' => 1009,
                'status' => 1,
                'requirement_flag' => 1,
                'deleted_at' => now(),  // 削除済
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

        // 基本パラメータ
        $this->baseParam = [
            'lp_order_id' => 1001,
            'goods_name' => '商品名_100101',
            'trademark_flag' => 0,
            'chinese_characters' => '漢字_100101',
            'kana' => 'カナ_100101',
            'romaji' => 'ローマ字_100101',
            'material_list' => [
                [
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
        ];
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
     * 正常
     *
     * @return void
     */
    public function test_正常()
    {
        $param = $this->baseParam;
        $formerItemKartesCount = TItemKarte::count();
        $formerRawMaterialsCount = TRawMaterial::count();

        $itemKarteId = $this->service->execCreate($param);

        // t_item_kartes登録情報のチェック
        $this->assertEquals($formerItemKartesCount + 1, TItemKarte::count());
        $itemKarte = TItemKarte::orderBy('id')->get()->last();
        $this->assertEquals($itemKarteId, $itemKarte->id);
        $expect = $param;
        $this->assertArrayEquals($expect, $itemKarte, ['id', 'material_list']);
        $this->assertNotNull($itemKarte->created_pg);
        $this->assertNotNull($itemKarte->updated_pg);

        // t_raw_materials登録情報のチェック
        $this->assertEquals($formerRawMaterialsCount + count($param['material_list']), TRawMaterial::count());
        $rawMaterials = TRawMaterial::where('item_karte_id', $itemKarte->id)
            ->orderBy('id')
            ->get();
        $this->assertCount(count($param['material_list']), $rawMaterials); 
        $rawMaterials->each(function ($rawMaterial, $i) use ($param) {
            $expect = $param['material_list'][$i];
            $this->assertArrayEquals($expect, $rawMaterial, ['id', 'item_karte_id']);
            $this->assertNotNull($rawMaterial->created_pg);
            $this->assertNotNull($rawMaterial->updated_pg);
        });
    }

    /**
     * 異常：LP構成情報非存在（例外発生）
     *
     * @return void
     */
    public function test_異常：LP構成情報非存在()
    {
        $param = $this->baseParam;
        $param['lp_order_id'] = 9001;

        $this->expectException(Exception::class);
        $this->service->execCreate($param);
    }

    /**
     * 異常：LP構成情報が削除済（例外発生）
     *
     * @return void
     */
    public function test_異常：LP構成情報が削除済み()
    {
        $param = $this->baseParam;
        $param['lp_order_id'] = 1009;

        $this->expectException(Exception::class);
        $this->service->execCreate($param);
    }
}
