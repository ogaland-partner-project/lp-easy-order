<?php

namespace Tests\Feature\Api\ItemKarteApi;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Common\CommonMsg;
use App\Models\TLpOrder;
use App\Models\TItemKarte;
use App\Models\TRawMaterial;

class UpdateTest extends TestCase
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
                'id' => 100109,
                'lp_order_id' => 1001,
                'trademark_flag' => 0,
                'price_including_tax' => 90000,
                'target_jendar' => '男性',
                'target_age' => 90,
                'deleted_at' => now(),      // 削除済
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
        collect([
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
                'id' => 10010103,
                'item_karte_id' => 100101,
                'production_area_publish_flag' => 0,
                'photo_material_flag' => 1,
                'certificate_flag' => 0,
                'coverage_content_flag' => 1,
            ],
            [
                'id' => 10010104,
                'item_karte_id' => 100101,
                'production_area_publish_flag' => 1,
                'photo_material_flag' => 0,
                'certificate_flag' => 1,
                'coverage_content_flag' => 0,
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
            'goods_name' => '商品名_100101_update',
            'trademark_flag' => 1,
            'chinese_characters' => '漢字_100101_update',
            'kana' => 'カナ_100101_update',
            'romaji' => 'ローマ字_100101_update',
            'material_list' => [
                // 更新1
                [
                    'id' => 10010101,
                    'item_karte_id' => 100101,
                    'raw_material_name' => '原材料_10010101_update',
                    'production_area' => '産地_10010101_update',
                    'raw_material_detail' => '詳細_10010101_update',
                    'production_area_publish_flag' => 1,
                    'photo_material_flag' => 1,
                    'certificate_flag' => 1,
                    'coverage_content_flag' => 1,
                    'document_path' => 'storage/documents/10010101_update.doc',
                ],
                // 新規追加1（id値無し）
                [
                    'item_karte_id' => 100101,
                    'raw_material_name' => '原材料_100101_new1',
                    'production_area' => '産地_100101_new1',
                    'raw_material_detail' => '詳細_100101_new1',
                    'production_area_publish_flag' => 0,
                    'photo_material_flag' => 0,
                    'certificate_flag' => 0,
                    'coverage_content_flag' => 0,
                    'document_path' => 'storage/documents/100101_new1.doc',
                ],
                // 更新2
                [
                    'id' => 10010103,
                    'item_karte_id' => 100101,
                    'raw_material_name' => '原材料_10010103_update',
                    'production_area' => '産地_10010103_update',
                    'raw_material_detail' => '詳細_10010103_update',
                    'production_area_publish_flag' => 1,
                    'photo_material_flag' => 0,
                    'certificate_flag' => 1,
                    'coverage_content_flag' => 0,
                    'document_path' => 'storage/documents/10010101_update.doc',
                ],
                // 新規追加2（id=null）
                [
                    'id' => null,
                    'item_karte_id' => 100101,
                    'raw_material_name' => '原材料_100101_new2',
                    'production_area' => '産地_100101_new2',
                    'raw_material_detail' => '詳細_100101_new2',
                    'production_area_publish_flag' => 1,
                    'photo_material_flag' => 1,
                    'certificate_flag' => 1,
                    'coverage_content_flag' => 1,
                    'document_path' => 'storage/documents/100101_new2.doc',
                ],
            ],
            'goods_specifications' => '商品仕様_100101_update',
            'price_including_tax' => 10001,
            'jas_mark' => 'J100101_update',
            'jas_mark_folder' => 'storage/jas/100101_update.svg',
            'jas_mark_certification' => 'JASマーク認定_100101_update',
            'target_jendar' => '女性',
            'target_age' => 11,
            'target_statue' => 'ターゲット層_100101_update',
            'BM_goods_name1' => 'BM商品名1_100101_update',
            'BM_url1' => 'https://url1.com/100101_update',
            'BM_goods_name2' => 'BM商品名2_100101_update',
            'BM_url2' => 'https://url2.com/100101_update',
            'difference_point' => '差別化ポイント_100101_update',
            'concept' => 'コンセプト_100101_update',
            'supplier_information_sharing' => '業者情報共有欄_100101_update',
            'strong_point' => '強み_100101_update',
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
        $itemKarteId = 100101;
        $param = $this->baseParam;
        $beforeRawMaterials = TRawMaterial::where('item_karte_id', $itemKarteId)->orderBy('id')->get();
        $otherItemKarteId = 100102;
        $beforeOtherItemKartes = TItemKarte::find($otherItemKarteId);
        $beforeOtherRawMaterials = TRawMaterial::where('item_karte_id', $otherItemKarteId)->orderBy('id')->get();

        $response = $this->json('PUT', self::API_URL . $itemKarteId, $param);
        $response->assertStatus(200)
            ->assertJson([
                'dataArray' => '',
                'normalMessage' => CommonMsg::MSG_ID_000002,
                'errorMessage' => '',
            ]);

        // 商品カルテ情報が更新されていること
        $itemKarte = TItemKarte::find($itemKarteId);
        $expect = array_merge($param, [
            'id' => $itemKarteId,
            'lp_order_id' => $itemKarte->lp_order_id, 
        ]);
        $this->assertArrayEquals($expect, $itemKarte, ['material_list']);

        // 原材料情報のチェック
        $rawMaterials = TRawMaterial::where('item_karte_id', $itemKarte->id)
            ->orderBy('id')
            ->get();

        // - id指定した原材料情報は更新されていること
        $updateRows = collect($param['material_list'])->filter(function ($row) {
            return isset($row['id']);
        });
        $updateRows->each(function ($row) use ($itemKarteId) {
            $rawMaterial = TRawMaterial::find($row['id']);
            $this->assertArrayEquals($row, $rawMaterial);
        });

        // - id指定のない原材料情報は新規登録されていること
        $createRows = collect($param['material_list'])->filter(function ($row) {
            return !isset($row['id']);
        });
        $createRows->each(function ($row) use ($itemKarteId) {
            $rawMaterial = TRawMaterial::where('raw_material_name', $row['raw_material_name'])->first();
            $this->assertArrayEquals($row, $rawMaterial, ['id']);
        });

        // - パラメータで指定されていない原材料情報は削除されていること
        $ids = $updateRows->map(function ($row) {
            return $row['id'];
        });
        $deleteRawMaterials = $beforeRawMaterials->filter(function ($rawMaterial) use ($ids) {
            return !$ids->contains($rawMaterial->id);
        });
        $deleteRawMaterials->each(function ($rawMaterial) {
            $this->assertNull(TRawMaterial::find($rawMaterial->id));
        });
    }

    /**
     * 異常：商品カルテIDが非数値
     *
     * @return void
     */
    public function test_異常：商品カルテIDが非数値()
    {
        $itemKarteId = '１００１０１';
        $param = $this->baseParam;

        $response = $this->json('PUT', self::API_URL . $itemKarteId, $param);
        $response->assertStatus(200)
            ->assertJson([
                'dataArray' => [],
                'normalMessage' => '',
                'errorMessage' => CommonMsg::MSG_ID_000006,
            ]);
    }

    /**
     * 異常：商品カルテ情報が非存在
     *
     * @return void
     */
    public function test_異常：商品カルテ情報が非存在()
    {
        $itemKarteId = 900000;      # 非存在
        $param = $this->baseParam;

        $response = $this->json('PUT', self::API_URL . $itemKarteId, $param);
        $response->assertStatus(200)
            ->assertJson([
                'dataArray' => [],
                'normalMessage' => '',
                'errorMessage' => CommonMsg::MSG_ID_000006,
            ]);
    }

    /**
     * 異常：商品カルテ情報が削除済
     *
     * @return void
     */
    public function test_異常：商品カルテ情報が削除済()
    {
        $itemKarteId = 100109;    # 削除済
        $param = $this->baseParam;

        $response = $this->json('PUT', self::API_URL . $itemKarteId, $param);
        $response->assertStatus(200)
            ->assertJson([
                'dataArray' => [],
                'normalMessage' => '',
                'errorMessage' => CommonMsg::MSG_ID_000006,
            ]);
    }

    /**
     * 異常：原材料IDが非数値
     *
     * @return void
     */
    public function test_異常：原材料IDが非数値()
    {
        $itemKarteId = 100101;
        $param = $this->baseParam;
        $param['material_list'][0]['id'] = '１００１０１０１';

        $response = $this->json('PUT', self::API_URL . $itemKarteId, $param);
        $response->assertStatus(422)
            ->assertJson([
                'dataArray' => [],
                'normalMessage' => '',
                'errorMessage' => '原材料IDは整数で指定してください。',
            ]);
    }

    /**
     * 異常：原材料情報が非存在
     *
     * @return void
     */
    public function test_異常：原材料情報が非存在()
    {
        $itemKarteId = 100101;
        $param = $this->baseParam;
        $param['material_list'][0]['id'] = 90000000;    # 非存在

        $response = $this->json('PUT', self::API_URL . $itemKarteId, $param);
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
     * 異常：原材料情報が削除済
     *
     * @return void
     */
    public function test_異常：原材料情報が削除済()
    {
        $itemKarteId = 100101;
        $param = $this->baseParam;
        $param['material_list'][0]['id'] = 10010109;    # 削除済

        $response = $this->json('PUT', self::API_URL . $itemKarteId, $param);
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
     * 異常：紐付く商品カルテ情報が異なる
     *
     * @return void
     */
    public function test_異常：紐付く商品カルテ情報が異なる()
    {
        $itemKarteId = 100101;
        $param = $this->baseParam;
        $param['material_list'][0]['id'] = 10010201;    # 商品カルテID=100102

        $response = $this->json('PUT', self::API_URL . $itemKarteId, $param);
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
     * 異常：商品登録可能性が非数値
     *
     * @return void
     */
    public function test_異常：商品登録可能性が非数値()
    {
        $itemKarteId = 100101;
        $param = $this->baseParam;
        $param['trademark_flag'] = '１';

        $response = $this->json('PUT', self::API_URL . $itemKarteId, $param);
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
        $itemKarteId = 100101;
        $param = $this->baseParam;
        $param['material_list'][1]['production_area_publish_flag'] = '１';

        $response = $this->json('PUT', self::API_URL . $itemKarteId, $param);
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
        $itemKarteId = 100101;
        $param = $this->baseParam;
        $param['material_list'][1]['photo_material_flag'] = '１';

        $response = $this->json('PUT', self::API_URL . $itemKarteId, $param);
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
        $itemKarteId = 100101;
        $param = $this->baseParam;
        $param['material_list'][1]['certificate_flag'] = '１';

        $response = $this->json('PUT', self::API_URL . $itemKarteId, $param);
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
        $itemKarteId = 100101;
        $param = $this->baseParam;
        $param['material_list'][1]['coverage_content_flag'] = '１';

        $response = $this->json('PUT', self::API_URL . $itemKarteId, $param);
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
        $itemKarteId = 100101;
        $param = $this->baseParam;
        $param['price_including_tax'] = '１０００';

        $response = $this->json('PUT', self::API_URL . $itemKarteId, $param);
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
        $itemKarteId = 100101;
        $param = $this->baseParam;
        $param['target_age'] = '３０';

        $response = $this->json('PUT', self::API_URL . $itemKarteId, $param);
        $response->assertStatus(422)
            ->assertJson([
                'dataArray' => [],
                'normalMessage' => '',
                'errorMessage' => 'ターゲット年齢層は整数で指定してください。',
            ]);
    }
}
