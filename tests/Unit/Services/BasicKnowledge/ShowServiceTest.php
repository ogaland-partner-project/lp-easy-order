<?php

namespace Tests\Unit\Services\BasicKnowledge;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\BasicKnowledge\ShowService;
use App\Models\TLpOrder;
use App\Models\TBasicKnowledge;
use App\Models\TBasicKnowledgeDetail;
use App\Models\TBasicKnowledgeImage;
use App\Models\TBasicKnowledgeUrl;

class ShowServiceTest extends TestCase
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

        $this->service = new ShowService();

        // t_lp_ordersテーブル
        collect([
            [
                'id' => 1001,
                'status' => 0,
                'requirement_flag' => 0,
            ],
            // 対象外
            [
                'id' => 2001,
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

        // t_basic_knowledgesテーブル
        collect([
            [
                'id' => 100101,
                'lp_order_id' => 1001,
            ],
            [
                'id' => 100102,
                'lp_order_id' => 1001,
            ],
            [
                'id' => 100109,
                'lp_order_id' => 1001,
                'deleted_at' => now(),  // 削除済
            ],
            // 対象外
            [
                'id' => 200101,
                'lp_order_id' => 2001,
            ],
        ])->each(function ($row) {
            factory(TBasicKnowledge::class)->create(
                array_merge($row, [
                    'question' => '質問_' . $row['id'],
                    'others' => 'その他_' . $row['id'],
                ])
            );
        });

        // t_basic_knowledge_detailsテーブル
        collect([
            [
                'id' => 10010101,
                'basic_knowledge_id' => 100101,
                'col' => 20,
                'sort_order' => 20,
            ],
            [
                'id' => 10010102,
                'basic_knowledge_id' => 100101,
                'col' => 10,
                'sort_order' => 10,
            ],
            [
                'id' => 10010103,
                'basic_knowledge_id' => 100101,
                'col' => 10,
                'sort_order' => 20,
            ],
            [
                'id' => 10010104,
                'basic_knowledge_id' => 100101,
                'col' => 20,
                'sort_order' => 10,
            ],
            [
                'id' => 10010109,
                'basic_knowledge_id' => 100101,
                'col' => 10,
                'sort_order' => 11,
                'deleted_at' => now(),  // 削除済
            ],
            [
                'id' => 10010201,
                'basic_knowledge_id' => 100102,
                'col' => 10,
                'sort_order' => 10,
            ],
            // 対象外
            [
                'id' => 20010101,
                'basic_knowledge_id' => 200101,
                'col' => 10,
                'sort_order' => 10,
            ],
        ])->each(function ($row) {
            factory(TBasicKnowledgeDetail::class)->create(
                array_merge($row, [
                    'title' => 'タイトル_' . $row['id'],
                    'detail' => '内容_' . $row['id'],
                ])
            );
        });

        // t_basic_knowledge_imagesテーブル
        collect([
            [
                'id' => 10010101,
                'basic_knowledge_id' => 100101,
            ],
            [
                'id' => 10010102,
                'basic_knowledge_id' => 100101,
            ],
            [
                'id' => 10010109,
                'basic_knowledge_id' => 100101,
                'deleted_at' => now(),  // 削除済
            ],
            [
                'id' => 10010201,
                'basic_knowledge_id' => 100102,
            ],
            // 対象外
            [
                'id' => 20010101,
                'basic_knowledge_id' => 200101,
            ],
        ])->each(function ($row) {
            factory(TBasicKnowledgeImage::class)->create(
                array_merge($row, [
                    'image_path' => 'storage/basic_knowledge_images/' . $row['id'] . '.jpg',
                    'image_memo' => '画像メモ_' . $row['id'],
                ])
            );
        });

        // t_basic_knowledge_urlsテーブル
        collect([
            [
                'id' => 10010101,
                'basic_knowledge_id' => 100101,
            ],
            [
                'id' => 10010102,
                'basic_knowledge_id' => 100101,
            ],
            [
                'id' => 10010109,
                'basic_knowledge_id' => 100101,
                'deleted_at' => now(),  // 削除済
            ],
            [
                'id' => 10010201,
                'basic_knowledge_id' => 100102,
            ],
            // 対象外
            [
                'id' => 20010101,
                'basic_knowledge_id' => 200101,
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
        $expect = [
            [
                'question' => '質問_100101',
                'others' => 'その他_100101',
                'details' => [
                    // col=10
                    [
                        [
                            'id' => 10010102,
                            'title' => 'タイトル_10010102',
                            'detail' => '内容_10010102',
                            'col' => 10,
                            'sort_order' => 10,
                        ],
                        [
                            'id' => 10010103,
                            'title' => 'タイトル_10010103',
                            'detail' => '内容_10010103',
                            'col' => 10,
                            'sort_order' => 20,
                        ],
                    ],
                    // col=20
                    [
                        [
                            'id' => 10010104,
                            'title' => 'タイトル_10010104',
                            'detail' => '内容_10010104',
                            'col' => 20,
                            'sort_order' => 10,
                        ],
                        [
                            'id' => 10010101,
                            'title' => 'タイトル_10010101',
                            'detail' => '内容_10010101',
                            'col' => 20,
                            'sort_order' => 20,
                        ],
                    ],
                ],
                'images' => [
                    [
                        'id' => 10010101,
                        'image_path' => 'storage/basic_knowledge_images/10010101.jpg',
                        'image_memo' => '画像メモ_10010101',
                    ],
                    [
                        'id' => 10010102,
                        'image_path' => 'storage/basic_knowledge_images/10010102.jpg',
                        'image_memo' => '画像メモ_10010102',
                    ],
                ],
                'urls' => [
                    [
                        'id' => 10010101,
                        'url' => 'https://example.com/10010101',
                    ],
                    [
                        'id' => 10010102,
                        'url' => 'https://example.com/10010102',
                    ],
                ],
            ],
            [
                'question' => '質問_100102',
                'others' => 'その他_100102',
                'details' => [
                    [
                        [
                            'id' => 10010201,
                            'title' => 'タイトル_10010201',
                            'detail' => '内容_10010201',
                            'col' => 10,
                            'sort_order' => 10,
                        ],
                    ],
                ],
                'images' => [
                    [
                        'id' => 10010201,
                        'image_path' => 'storage/basic_knowledge_images/10010201.jpg',
                        'image_memo' => '画像メモ_10010201',
                    ],
                ],
                'urls' => [
                    [
                        'id' => 10010201,
                        'url' => 'https://example.com/10010201',
                    ],
                ],
            ],
        ];

        $actual = $this->service->execShow(1001);
        $this->assertSame($expect, $actual);
    }

    /**
     * 正常：該当レコードなし
     *
     * @return void
     */
    public function test_正常：該当レコードなし()
    {
        $actual = $this->service->execShow(9001);
        $this->assertSame([], $actual);
    }
}
