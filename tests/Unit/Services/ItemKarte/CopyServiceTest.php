<?php

namespace Tests\Unit\Services\ItemKarte;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Common\CommonMsg;
use App\Exceptions\AppException;
use App\Services\ItemKarte\CopyService;
use App\Models\TLpOrder;
use App\Models\TItemKarte;
use App\Models\TRawMaterial;

class CopyServiceTest extends TestCase
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

        $this->service = new CopyService();

        // t_lp_ordersテーブル
        collect([
            [
                'id' => 1001,
                'status' => 0,
                'requirement_flag' => 0,
            ],
            [
                'id' => 1009,
                'status' => 0,
                'requirement_flag' => 0,
                'deleted_at' => now(),      // 削除済
            ],
            [
                'id' => 2001,
                'status' => 1,
                'requirement_flag' => 1,
            ],
            [
                'id' => 2009,
                'status' => 1,
                'requirement_flag' => 1,
                'deleted_at' => now(),      // 削除済
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
                'trademark_flag' => 0,
                'price_including_tax' => 30000,
                'target_jendar' => '男性',
                'target_age' => 30,
            ],
            [
                'id' => 200101,
                'lp_order_id' => 2001,
                'trademark_flag' => 1,
                'price_including_tax' => 40000,
                'target_jendar' => '女性',
                'target_age' => 40,
            ],
            [
                'id' => 200102,
                'lp_order_id' => 2001,
                'trademark_flag' => 0,
                'price_including_tax' => 50000,
                'target_jendar' => '男性',
                'target_age' => 50,
            ],
            [
                'id' => 200109,
                'lp_order_id' => 2001,
                'trademark_flag' => 1,
                'price_including_tax' => 60000,
                'target_jendar' => '女性',
                'target_age' => 60,
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
                'id' => 10010201,
                'item_karte_id' => 100102,
                'production_area_publish_flag' => 0,
                'photo_material_flag' => 1,
                'certificate_flag' => 0,
                'coverage_content_flag' => 1,
            ],
            [
                'id' => 20010101,
                'item_karte_id' => 200101,
                'production_area_publish_flag' => 1,
                'photo_material_flag' => 1,
                'certificate_flag' => 1,
                'coverage_content_flag' => 1,
            ],
            [
                'id' => 20010102,
                'item_karte_id' => 200101,
                'production_area_publish_flag' => 0,
                'photo_material_flag' => 0,
                'certificate_flag' => 0,
                'coverage_content_flag' => 0,
            ],
            [
                'id' => 20010109,
                'item_karte_id' => 200101,
                'production_area_publish_flag' => 0,
                'photo_material_flag' => 0,
                'certificate_flag' => 0,
                'coverage_content_flag' => 0,
                'deleted_at' => now(),      // 削除済
            ],
            [
                'id' => 20010201,
                'item_karte_id' => 200102,
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
     * 正常
     *
     * @return void
     */
    public function test_正常()
    {
        $param = [
            'lp_order_id' => 1001,
            'other_lp_order_id' => 2001,
        ];
        $beforeItemKartes = TItemKarte::where('lp_order_id', $param['lp_order_id'])
            ->orderBy('id')->get();
        $otherItemKartes = TItemKarte::where('lp_order_id', $param['other_lp_order_id'])
            ->orderBy('id')->get();

        $this->service->execCopy($param);

        // 実行前にコピー先に紐付いていた商品カルテ情報が削除されていること
        $beforeItemKartes->each(function ($itemKarte) {
            $this->assertNull(TItemKarte::find($itemKarte->id));
            $itemKarte->rawMaterials->each(function ($rawMaterial) {
                $this->assertNull(TRawMaterial::find($rawMaterial->id));
            });
        });

        $itemKartes = TItemKarte::where('lp_order_id', $param['lp_order_id'])->orderBy('id')->get();
        // コピー元とコピー先の商品カルテ情報の個数が一致すること
        $this->assertCount($otherItemKartes->count(), $itemKartes);
        $itemKartes->each(function ($itemKarte, $i) use ($param, $otherItemKartes) {
            // 各商品カルテ情報のコピー元とコピー先が一致すること
            $fromItemKarte = $otherItemKartes->skip($i)->first();
            $fromItemKarte->lp_order_id = $param['lp_order_id'];
            $this->assertArrayEquals($fromItemKarte, $itemKarte, ['id', 'raw_materials']);
            // コピー元とコピー先の現材料情報の個数が一致すること
            $this->assertCount($fromItemKarte->rawMaterials->count(), $itemKarte->rawMaterials);
            $itemKarte->rawMaterials->each(function ($rawMaterial, $i) use ($fromItemKarte) {
                // 各現材料情報のコピー元とコピー先が一致すること
                $fromRawMaterial = $fromItemKarte->rawMaterials->skip($i)->first();
                $this->assertArrayEquals($fromRawMaterial, $rawMaterial, ['id', 'item_karte_id']);
            });
        });
    }

    /**
     * 異常：コピー先LP構成情報が非存在
     *
     * @return void
     */
    public function test_異常：コピー先LP構成情報が非存在()
    {
        $param = [
            'lp_order_id' => 9000,          # 非存在
            'other_lp_order_id' => 2001,
        ];

        $this->expectException(AppException::class);
        $this->expectExceptionMessage(CommonMsg::MSG_ID_000006);
        $this->service->execCopy($param);
    }

    /**
     * 異常：コピー先LP構成情報が削除済
     *
     * @return void
     */
    public function test_異常：コピー先LP構成情報が削除済()
    {
        $param = [
            'lp_order_id' => 1009,          # 削除済
            'other_lp_order_id' => 2001,
        ];

        $this->expectException(AppException::class);
        $this->expectExceptionMessage(CommonMsg::MSG_ID_000006);
        $this->service->execCopy($param);
    }

    /**
     * 異常：コピー元LP構成情報が非存在
     *
     * @return void
     */
    public function test_異常：コピー元LP構成情報が非存在()
    {
        $param = [
            'lp_order_id' => 1001,
            'other_lp_order_id' => 9000,    # 非存在
        ];

        $this->expectException(AppException::class);
        $this->expectExceptionMessage(CommonMsg::MSG_ID_000006);
        $this->service->execCopy($param);
    }

    /**
     * 異常：コピー元LP構成情報が削除済
     *
     * @return void
     */
    public function test_異常：コピー元LP構成情報が削除済()
    {
        $param = [
            'lp_order_id' => 1001,
            'other_lp_order_id' => 2009,    # 削除済
        ];

        $this->expectException(AppException::class);
        $this->expectExceptionMessage(CommonMsg::MSG_ID_000006);
        $this->service->execCopy($param);
    }
}
