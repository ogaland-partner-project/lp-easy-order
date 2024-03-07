<?php

namespace Tests\Feature\Api\ItemKarteApi;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Common\CommonMsg;
use App\Models\TLpOrder;
use App\Models\TItemKarte;
use App\Models\TRawMaterial;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    const API_URL = '/api/lp_easy_order/item_karte';

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

        $response = $this->json('POST', self::API_URL, $param);
        $response->assertStatus(200)
            ->assertJson([
                'dataArray' => '',
                'normalMessage' => CommonMsg::MSG_ID_000001,
                'errorMessage' => '',
            ]);

        // t_item_kartes登録情報のチェック
        $this->assertEquals($formerItemKartesCount + 1, TItemKarte::count());
        $itemKarte = TItemKarte::orderBy('id')->get()->last();
        $expect = $param;
        $this->assertArrayEquals($expect, $itemKarte, ['id', 'material_list']);

        // t_raw_materials登録情報のチェック
        $this->assertEquals($formerRawMaterialsCount + count($param['material_list']), TRawMaterial::count());
        $rawMaterials = TRawMaterial::where('item_karte_id', $itemKarte->id)
            ->orderBy('id')
            ->get();
        $this->assertCount(count($param['material_list']), $rawMaterials); 
        $rawMaterials->each(function ($rawMaterial, $i) use ($param) {
            $expect = $param['material_list'][$i];
            $this->assertArrayEquals($expect, $rawMaterial, ['id', 'item_karte_id']);
        });
    }

    /**
     * 異常：LP構成IDなし
     *
     * @return void
     */
    public function test_異常：LP構成IDなし()
    {
        $param = $this->baseParam;
        unset($param['lp_order_id']);

        $response = $this->json('POST', self::API_URL, $param);
        $response->assertStatus(422)
            ->assertJson([
                'dataArray' => [],
                'normalMessage' => '',
                'errorMessage' => 'LP構成IDは必ず指定してください。',
            ]);
    }

    /**
     * 異常：LP構成ID非存在
     *
     * @return void
     */
    public function test_異常：LP構成ID非存在()
    {
        $param = $this->baseParam;
        $param['lp_order_id'] = 9000;

        $response = $this->json('POST', self::API_URL, $param);
        $response->assertStatus(500)
            ->assertJson([
                'dataArray' => [],
                'normalMessage' => '',
            ]);
        $this->assertGreaterThan(0, strlen($response['errorMessage']));
        $this->assertGreaterThan(0, strlen($response['errorFile']));
        $this->assertGreaterThan(0, $response['errorLine']);
        $this->assertGreaterThan(0, strlen($response['errorDetail']));
    }

    /**
     * 異常：LP構成ID削除済
     *
     * @return void
     */
    public function test_異常：LP構成ID削除済()
    {
        $param = $this->baseParam;
        $param['lp_order_id'] = 1009;

        $response = $this->json('POST', self::API_URL, $param);
        $response->assertStatus(500)
            ->assertJson([
                'dataArray' => [],
                'normalMessage' => '',
            ]);
        $this->assertGreaterThan(0, strlen($response['errorMessage']));
        $this->assertGreaterThan(0, strlen($response['errorFile']));
        $this->assertGreaterThan(0, $response['errorLine']);
        $this->assertGreaterThan(0, strlen($response['errorDetail']));
    }

    /**
     * 異常：LP構成IDが非数値
     *
     * @return void
     */
    public function test_異常：LP構成IDが非数値()
    {
        $param = $this->baseParam;
        $param['lp_order_id'] = '１００１';

        $response = $this->json('POST', self::API_URL, $param);
        $response->assertStatus(422)
            ->assertJson([
                'dataArray' => [],
                'normalMessage' => '',
                'errorMessage' => 'LP構成IDは整数で指定してください。',
            ]);
    }

    /**
     * 異常：商品登録可能性が非数値
     *
     * @return void
     */
    public function test_異常：商品登録可能性が非数値()
    {
        $param = $this->baseParam;
        $param['trademark_flag'] = '１';

        $response = $this->json('POST', self::API_URL, $param);
        $response->assertStatus(422)
            ->assertJson([
                'dataArray' => [],
                'normalMessage' => '',
                'errorMessage' => '商品登録可能性は整数で指定してください。',
            ]);
    }

    /**
     * 異常：原材料リスト・産地掲載の有無が非数値
     *
     * @return void
     */
    public function test_異常：原材料リスト・産地掲載の有無が非数値()
    {
        $param = $this->baseParam;
        $param['material_list'][1]['production_area_publish_flag'] = '１';

        $response = $this->json('POST', self::API_URL, $param);
        $response->assertStatus(422)
            ->assertJson([
                'dataArray' => [],
                'normalMessage' => '',
                'errorMessage' => '産地掲載の有無は整数で指定してください。',
            ]);
    }

    /**
     * 異常：原材料リスト・写真素材の有無が非数値
     *
     * @return void
     */
    public function test_異常：原材料リスト・写真素材の有無が非数値()
    {
        $param = $this->baseParam;
        $param['material_list'][1]['photo_material_flag'] = '１';

        $response = $this->json('POST', self::API_URL, $param);
        $response->assertStatus(422)
            ->assertJson([
                'dataArray' => [],
                'normalMessage' => '',
                'errorMessage' => '写真素材の有無は整数で指定してください。',
            ]);
    }

    /**
     * 異常：原材料リスト・証明書関係の有無が非数値
     *
     * @return void
     */
    public function test_異常：原材料リスト・証明書関係の有無が非数値()
    {
        $param = $this->baseParam;
        $param['material_list'][1]['certificate_flag'] = '１';

        $response = $this->json('POST', self::API_URL, $param);
        $response->assertStatus(422)
            ->assertJson([
                'dataArray' => [],
                'normalMessage' => '',
                'errorMessage' => '証明書関係の有無は整数で指定してください。',
            ]);
    }

    /**
     * 異常：原材料リスト・取材内容の有無が非数値
     *
     * @return void
     */
    public function test_異常：原材料リスト・取材内容の有無が非数値()
    {
        $param = $this->baseParam;
        $param['material_list'][1]['coverage_content_flag'] = '１';

        $response = $this->json('POST', self::API_URL, $param);
        $response->assertStatus(422)
            ->assertJson([
                'dataArray' => [],
                'normalMessage' => '',
                'errorMessage' => '取材内容の有無は整数で指定してください。',
            ]);
    }

    /**
     * 異常：税込販売価格の有無が非数値
     *
     * @return void
     */
    public function test_異常：税込販売価格が非数値()
    {
        $param = $this->baseParam;
        $param['price_including_tax'] = '１０００';

        $response = $this->json('POST', self::API_URL, $param);
        $response->assertStatus(422)
            ->assertJson([
                'dataArray' => [],
                'normalMessage' => '',
                'errorMessage' => '税込販売価格は整数で指定してください。',
            ]);
    }

    /**
     * 異常：ターゲット年齢層の有無が非数値
     *
     * @return void
     */
    public function test_異常：ターゲット年齢層が非数値()
    {
        $param = $this->baseParam;
        $param['target_age'] = '３０';

        $response = $this->json('POST', self::API_URL, $param);
        $response->assertStatus(422)
            ->assertJson([
                'dataArray' => [],
                'normalMessage' => '',
                'errorMessage' => 'ターゲット年齢層は整数で指定してください。',
            ]);
    }
}
