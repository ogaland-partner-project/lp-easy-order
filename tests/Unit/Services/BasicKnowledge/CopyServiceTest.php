<?php

namespace Tests\Unit\Services\BasicKnowledge;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Common\CommonMsg;
use App\Exceptions\AppException;
use App\Services\BasicKnowledge\CopyService;
use App\Models\TLpOrder;
use App\Models\TBasicKnowledge;
use App\Models\TBasicKnowledgeDetail;
use App\Models\TBasicKnowledgeImage;
use App\Models\TBasicKnowledgeUrl;

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

        // t_basic_knowledgesテーブル
        collect([
            // コピー先
            [
                'id' => 100101,
                'lp_order_id' => 1001,
            ],
            [
                'id' => 100102,
                'lp_order_id' => 1001,
            ],
            // コピー元
            [
                'id' => 200101,
                'lp_order_id' => 2001,
            ],
            [
                'id' => 200102,
                'lp_order_id' => 2001,
            ],
            [
                'id' => 200103,
                'lp_order_id' => 2001,
            ],
            [
                'id' => 200109,
                'lp_order_id' => 2001,
                'deleted_at' => now(),      // 削除済
            ],
        ])->each(function ($row) {
            factory(TBasicKnowledge::class)->create(
                array_merge($row, [
                    'question' => '疑問点_' . $row['id'],
                    'others' => 'その他_' . $row['id'],
                ])
            );
        });

        // t_basic_knowledge_detailsテーブル
        collect([
            // コピー先
            [
                'id' => 10010101,
                'basic_knowledge_id' => 100101,
                'col' => 0,
                'sort_order' => 0,
            ],
            [
                'id' => 10010102,
                'basic_knowledge_id' => 100101,
                'col' => 0,
                'sort_order' => 1,
            ],
            [
                'id' => 10010111,
                'basic_knowledge_id' => 100101,
                'col' => 1,
                'sort_order' => 0,
            ],
            [
                'id' => 10010112,
                'basic_knowledge_id' => 100101,
                'col' => 1,
                'sort_order' => 1,
            ],
            [
                'id' => 10010201,
                'basic_knowledge_id' => 100102,
                'col' => 0,
                'sort_order' => 0,
            ],
            // コピー元
            [
                'id' => 20010101,
                'basic_knowledge_id' => 200101,
                'col' => 0,
                'sort_order' => 0,
            ],
            [
                'id' => 20010102,
                'basic_knowledge_id' => 200101,
                'col' => 0,
                'sort_order' => 1,
            ],
            [
                'id' => 20010103,
                'basic_knowledge_id' => 200101,
                'col' => 0,
                'sort_order' => 2,
            ],
            [
                'id' => 20010111,
                'basic_knowledge_id' => 200101,
                'col' => 1,
                'sort_order' => 0,
            ],
            [
                'id' => 20010121,
                'basic_knowledge_id' => 200101,
                'col' => 2,
                'sort_order' => 0,
            ],
            [
                'id' => 20010201,
                'basic_knowledge_id' => 200102,
                'col' => 0,
                'sort_order' => 0,
            ],
            [
                'id' => 20010301,
                'basic_knowledge_id' => 200103,
                'col' => 0,
                'sort_order' => 0,
            ],
        ])->each(function ($row) {
            factory(TBasicKnowledgeDetail::class)->create(
                array_merge($row, [
                    'title' => 'タイトル_' . $row['col'] . '_' . $row['sort_order'],
                    'detail' => '内容_' . $row['col'] . '_' . $row['sort_order'],
                ])
            );
        });

        // t_basic_knowledge_imagesテーブル
        collect([
            // コピー先
            [
                'id' => 10010101,
                'basic_knowledge_id' => 100101,
            ],
            [
                'id' => 10010102,
                'basic_knowledge_id' => 100101,
            ],
            [
                'id' => 10010201,
                'basic_knowledge_id' => 100102,
            ],
            // コピー元
            [
                'id' => 20010101,
                'basic_knowledge_id' => 200101,
            ],
            [
                'id' => 20010201,
                'basic_knowledge_id' => 200102,
            ],
            [
                'id' => 20010202,
                'basic_knowledge_id' => 200102,
            ],
            [
                'id' => 20010301,
                'basic_knowledge_id' => 200103,
            ],
        ])->each(function ($row) {
            factory(TBasicKnowledgeImage::class)->create(
                array_merge($row, [
                    'image_path' => '/storage/basic_knowledge_images/' . $row['id'] . '.jpg',
                    'image_memo' => '画像メモ_' . $row['id'],
                ])
            );
        });

        // t_basic_knowledge_urlテーブル
        collect([
            // コピー先
            [
                'id' => 10010101,
                'basic_knowledge_id' => 100101,
            ],
            [
                'id' => 10010102,
                'basic_knowledge_id' => 100101,
            ],
            [
                'id' => 10010201,
                'basic_knowledge_id' => 100102,
            ],
            // コピー元
            [
                'id' => 20010101,
                'basic_knowledge_id' => 200101,
            ],
            [
                'id' => 20010201,
                'basic_knowledge_id' => 200102,
            ],
            [
                'id' => 20010202,
                'basic_knowledge_id' => 200102,
            ],
            [
                'id' => 20010301,
                'basic_knowledge_id' => 200103,
            ],
        ])->each(function ($row) {
            factory(TBasicKnowledgeUrl::class)->create(
                array_merge($row, [
                    'url' => 'https://example.com/' . $row['id'],
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
        $beforeBasicKnowledges = TBasicKnowledge::where('lp_order_id', $param['lp_order_id'])
            ->orderBy('id')->get();
        $otherBasicKnowledges = TBasicKnowledge::where('lp_order_id', $param['other_lp_order_id'])
            ->orderBy('id')->get();

        $this->service->execCopy($param);

        // 実行前にコピー先に紐付いていた基礎知識情報が削除されていること
        $beforeBasicKnowledges->each(function ($basicKnowledge) {
            $this->assertNull(TBasicKnowledge::find($basicKnowledge->id));
            $basicKnowledge->basicKnowledgeDetails->each(function ($basicKnowledgeDetail) {
                $this->assertNull(TBasicKnowledgeDetail::find($basicKnowledgeDetail->id));
            });
            $basicKnowledge->basicKnowledgeImages->each(function ($basicKnowledgeImage) {
                $this->assertNull(TBasicKnowledgeImage::find($basicKnowledgeImage->id));
            });
            $basicKnowledge->basicKnowledgeUrls->each(function ($basicKnowledgeUrl) {
                $this->assertNull(TBasicKnowledgeUrl::find($basicKnowledgeUrl->id));
            });
        });

        // 基礎知識
        $basicKnowledges = TBasicKnowledge::where('lp_order_id', $param['lp_order_id'])->orderBy('id')->get();
        // - コピー元とコピー先の個数が一致すること
        $this->assertCount($otherBasicKnowledges->count(), $basicKnowledges);
        $basicKnowledges->each(function ($basicKnowledge, $i) use ($param, $otherBasicKnowledges) {
            // - コピー元とコピー先が一致すること
            $fromBasicKnowledge = $otherBasicKnowledges->skip($i)->first();
            $fromBasicKnowledge->lp_order_id = $param['lp_order_id'];
            $this->assertArrayEquals($fromBasicKnowledge, $basicKnowledge, ['id', 'details', 'images', 'urls']);
            // 基礎知識詳細
            // - コピー元とコピー先の個数が一致すること
            $this->assertCount($fromBasicKnowledge->basicKnowledgeDetails->count(), $basicKnowledge->basicKnowledgeDetails);
            $basicKnowledge->basicKnowledgeDetails->each(function ($basicKnowledgeDetail, $i) use ($fromBasicKnowledge) {
                // - コピー元とコピー先が一致すること
                $fromBasicKnowledgeDetail = $fromBasicKnowledge->basicKnowledgeDetails->skip($i)->first();
                $this->assertArrayEquals($fromBasicKnowledgeDetail, $basicKnowledgeDetail, ['id', 'basic_knowledge_id']);
            });
            // 基礎知識画像
            // - コピー元とコピー先の個数が一致すること
            $this->assertCount($fromBasicKnowledge->basicKnowledgeImages->count(), $basicKnowledge->basicKnowledgeImages);
                // - コピー元とコピー先が一致すること
                $basicKnowledge->basicKnowledgeImages->each(function ($basicKnowledgeImage, $i) use ($fromBasicKnowledge) {
                $fromBasicKnowledgeImage = $fromBasicKnowledge->basicKnowledgeImages->skip($i)->first();
                $this->assertArrayEquals($fromBasicKnowledgeImage, $basicKnowledgeImage, ['id', 'basic_knowledge_id']);
            });
            // 基礎知識URL
            // - コピー元とコピー先の個数が一致すること
            $this->assertCount($fromBasicKnowledge->basicKnowledgeUrls->count(), $basicKnowledge->basicKnowledgeUrls);
                // - コピー元とコピー先が一致すること
                $basicKnowledge->basicKnowledgeUrls->each(function ($basicKnowledgeUrl, $i) use ($fromBasicKnowledge) {
                $fromBasicKnowledgeUrl = $fromBasicKnowledge->basicKnowledgeUrls->skip($i)->first();
                $this->assertArrayEquals($fromBasicKnowledgeUrl, $basicKnowledgeUrl, ['id', 'basic_knowledge_id']);
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
