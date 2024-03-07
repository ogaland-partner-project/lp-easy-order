<?php

namespace Tests\Feature\Api\BasicKnowledgeApi;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Common\CommonMsg;
use App\Models\TLpOrder;
use App\Models\TBasicKnowledge;
use App\Models\TBasicKnowledgeDetail;
use App\Models\TBasicKnowledgeImage;
use App\Models\TBasicKnowledgeUrl;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    const API_URL = '/api/lp_easy_order/basic_knowledge/';

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
            // col=0
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
                'id' => 10010109,
                'basic_knowledge_id' => 100101,
                'col' => 0,
                'sort_order' => 11,
                'deleted_at' => now(),      // 削除済
            ],
            // col=1
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
            // 対象外
            [
                'id' => 10010201,
                'basic_knowledge_id' => 100102,
                'col' => 0,
                'sort_order' => 0,
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
                'id' => 10010103,
                'basic_knowledge_id' => 100101,
            ],
            [
                'id' => 10010109,
                'basic_knowledge_id' => 100101,
                'deleted_at' => now(),  // 削除済
            ],
            // 対象外
            [
                'id' => 10010201,
                'basic_knowledge_id' => 100102,
            ],
        ])->each(function ($row) {
            factory(TBasicKnowledgeImage::class)->create(
                array_merge($row, [
                    'image_path' => sprintf(
                        '/storage/lp_order/%d/basicknowledge/%d.jpg',
                        floor($row['id'] / 1000),
                        $row['id']
                    ),
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
            // 対象外
            [
                'id' => 10010201,
                'basic_knowledge_id' => 100102,
            ],
        ])->each(function ($row) {
            factory(TBasicKnowledgeUrl::class)->create(
                array_merge($row, [
                    'url' => 'https://example.com/' . $row['id'],
                ])
            );
        });

        // 基本パラメータ
        $this->baseParam = [
            'details' => [
                // col:0
                [
                    // sort_order:1->0
                    [
                        'id' => 10010102,
                        'title' => 'タイトル_10010102_update',
                        'detail' => '詳細_10010102_update',
                    ],
                    // sort_order:1（新規）
                    [
                        'id' => null,
                        'title' => 'タイトル_100101_0_new1',
                        'detail' => '詳細_100101_0_new1',
                    ],
                    // sort_order:2（新規）
                    [
                        'id' => null,
                        'title' => 'タイトル_100101_0_new2',
                        'detail' => '詳細_100101_0_new2',
                    ],
                ],
                // col:1
                [
                    // sort_order:0（新規）
                    [
                        'id' => null,
                        'title' => 'タイトル_100101_1_new',
                        'detail' => '詳細_100101_1_new',
                    ],
                    // sort_order:1->削除
                ],
                // col:2（新規）
                [
                    // sort_order:0（新規）
                    [
                        'id' => null,
                        'title' => 'タイトル_100101_2_new',
                        'detail' => '詳細_100101_2_new',
                    ],
                ],
            ],
            'images' => [
                // 更新（画像なし）
                [
                    'id' => 10010102,
                    'image_path' => '',
                    'file' => null,
                    'image_memo' => '画像メモ_10010102_update',
                ],
                // 更新（画像あり）
                [
                    'id' => 10010103,
                    'image_path' => '',
                    'file' => $this->getImageFileString('tests/resources/BasicKnowledge/image.jpg'),
                    'image_memo' => '画像メモ_10010102_update',
                ],
                // 追加
                [
                    'id' => null,
                    'image_path' => '',
                    'file' => $this->getImageFileString('tests/resources/BasicKnowledge/image.png'),
                    'image_memo' => '画像メモ_100101_new',
                ],
            ],
            'urls' => [
                // 更新
                [
                    'id' => 10010102,
                    'url' => 'https://example.com/10010102_update',
                ],
                // 追加
                [
                    'id' => null,
                    'url' => 'https://example.com/100101_new',
                ],
            ],
            'question' => '疑問点_100101_update',
            'others' => 'その他_100101_update',
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
        $basicKnowledgeId = 100101;
        $param = $this->baseParam;

        $response = $this->json('PUT', self::API_URL . $basicKnowledgeId, $param);
        $response->assertStatus(200)
            ->assertJson([
                'dataArray' => '',
                'normalMessage' => CommonMsg::MSG_ID_000002,
                'errorMessage' => '',
            ]);

        // 基礎知識が更新されていること
        $basicKnowledge = TBasicKnowledge::find($basicKnowledgeId);
        $expect = array_merge($param, [
            'id' => $basicKnowledge->id,
            'lp_order_id' => $basicKnowledge->lp_order_id, 
        ]);
        $this->assertArrayEquals($expect, $basicKnowledge, ['details', 'images', 'urls']);

        // 基礎知識詳細のチェック
        collect($param['details'])->each(function ($rows, $colIndex) use ($basicKnowledge) {
            // - col内のレコード数が一致すること
            $rowsCount = TBasicKnowledgeDetail::where([
                'basic_knowledge_id' => $basicKnowledge->id,
                'col' => $colIndex,
            ])->count();
            $this->assertEquals(count($rows), $rowsCount);

            collect($rows)->each(function ($row, $i) use ($colIndex, $basicKnowledge) {
                $basicKnowledgeDetail = TBasicKnowledgeDetail::where([
                    'basic_knowledge_id' => $basicKnowledge->id,
                    'col' => $colIndex,
                    'sort_order' => $i,
                ])->first();
                $this->assertNotNull($basicKnowledgeDetail);
                if (isset($row['id']) && !is_null($row['id'])) {
                    // - 更新レコードのチェック
                    $expect = array_merge($row, [
                        'id' => $row['id'],
                        'basic_knowledge_id' => $basicKnowledge->id, 
                        'col' => $colIndex,
                        'sort_order' => $i,
                    ]);
                    $this->assertArrayEquals($expect, $basicKnowledgeDetail);
                } else {
                    // - 追加レコードのチェック
                    $expect = array_merge($row, [
                        'basic_knowledge_id' => $basicKnowledge->id, 
                        'col' => $colIndex,
                        'sort_order' => $i,
                    ]);
                    $this->assertArrayEquals($expect, $basicKnowledgeDetail, ['id']);
                }
            });
        });

        // - 最大col番号以上のレコードが存在しないこと
        $othersCount = TBasicKnowledgeDetail::where('basic_knowledge_id', $basicKnowledge->id)
            ->where('col', '>=', count($param['details']))
            ->count();
        $this->assertEquals(0, $othersCount);

        // 基礎知識画像のチェック
        // - レコード件数が一致すること
        $rowsCount = TBasicKnowledgeImage::where([
            'basic_knowledge_id' => $basicKnowledge->id,
        ])->count();
        $this->assertEquals(count($param['images']), $rowsCount);

        collect($param['images'])->each(function ($row) use ($basicKnowledge) {
            $imagePath = null;
            if (isset($row['id']) && !is_null($row['id'])) {
                // - id指定した情報は更新されていること
                $basicKnowledgeImage = TBasicKnowledgeImage::find($row['id']);
                // - fileパラメータがセットされている場合は画像ファイルパスを照会する
                if (isset($row['file']) && !is_null($row['file'])) {
                    preg_match('/^data:image\/(\w+);base64,.+$/', $row['file'], $matches);
                    $imagePath = sprintf(
                        'lp_order/%d/basicknowledge/%d.%s',
                        $basicKnowledge->lp_order_id,
                        $basicKnowledgeImage->id,
                        $matches[1]
                    );
                }
                $expect = array_merge($row, [
                    'basic_knowledge_id' => $basicKnowledge->id,
                    'image_path' => $imagePath ? '/storage/' . $imagePath : $basicKnowledgeImage->image_path,
                ]);
                $this->assertArrayEquals($expect, $basicKnowledgeImage, ['file']);
            } else {
                // - id指定のない情報は新規登録されていること
                $basicKnowledgeImage = TBasicKnowledgeImage::where('image_memo', $row['image_memo'])->first();
                preg_match('/^data:image\/(\w+);base64,.+$/', $row['file'], $matches);
                $imagePath = sprintf(
                    'lp_order/%d/basicknowledge/%d.%s',
                    $basicKnowledge->lp_order_id,
                    $basicKnowledgeImage->id,
                    $matches[1]
                );
                $expect = array_merge($row, [
                    'basic_knowledge_id' => $basicKnowledge->id,
                    'image_path' => '/storage/' . $imagePath,
                ]);
                $this->assertArrayEquals($expect, $basicKnowledgeImage, ['id', 'file']);
            }
            // - 画像ファイルの生成チェック
            if ($imagePath) {
                $this->assertTrue(Storage::exists($imagePath));
            }
        });

        // 基礎知識URLのチェック
        // - レコード件数が一致すること
        $rowsCount = TBasicKnowledgeUrl::where([
            'basic_knowledge_id' => $basicKnowledge->id,
        ])->count();
        $this->assertEquals(count($param['urls']), $rowsCount);

        collect($param['urls'])->each(function ($row) use ($basicKnowledge) {
            if (isset($row['id']) && !is_null($row['id'])) {
                // - id指定した情報は更新されていること
                $basicKnowledgeUrl = TBasicKnowledgeUrl::find($row['id']);
                $expect = array_merge($row, [
                    'basic_knowledge_id' => $basicKnowledge->id,
                ]);
                $this->assertArrayEquals($expect, $basicKnowledgeUrl);
            } else {
                // - id指定のない情報は新規登録されていること
                $basicKnowledgeUrl = TBasicKnowledgeUrl::where('url', $row['url'])->first();
                $expect = array_merge($row, [
                    'basic_knowledge_id' => $basicKnowledge->id,
                ]);
                $this->assertArrayEquals($expect, $basicKnowledgeUrl, ['id']);
            }
        });
    }

    /**
     * 異常：基礎知識IDが非数値
     *
     * @return void
     */
    public function test_異常：基礎知識IDが非数値()
    {
        $basicKnowledgeId = '１００１０１';
        $param = $this->baseParam;

        $response = $this->json('PUT', self::API_URL . $basicKnowledgeId, $param);
        $response->assertStatus(200)
            ->assertJson([
                'dataArray' => [],
                'normalMessage' => '',
                'errorMessage' => CommonMsg::MSG_ID_000006,
            ]);
    }

    /**
     * 異常：基礎知識IDが非存在
     *
     * @return void
     */
    public function test_異常：基礎知識IDが非存在()
    {
        $basicKnowledgeId = 900000;     # 非存在
        $param = $this->baseParam;

        $response = $this->json('PUT', self::API_URL . $basicKnowledgeId, $param);
        $response->assertStatus(200)
            ->assertJson([
                'dataArray' => [],
                'normalMessage' => '',
                'errorMessage' => CommonMsg::MSG_ID_000006,
            ]);
    }

    /**
     * 異常：基礎知識IDが削除済
     *
     * @return void
     */
    public function test_異常：基礎知識IDが削除済()
    {
        $basicKnowledgeId = 100109;     # 削除済
        $param = $this->baseParam;

        $response = $this->json('PUT', self::API_URL . $basicKnowledgeId, $param);
        $response->assertStatus(200)
            ->assertJson([
                'dataArray' => [],
                'normalMessage' => '',
                'errorMessage' => CommonMsg::MSG_ID_000006,
            ]);
    }

    /**
     * 異常：基礎知識詳細が非存在
     *
     * @return void
     */
    public function test_異常：基礎知識詳細が非存在()
    {
        $basicKnowledgeId = 100101;
        $param = $this->baseParam;
        $param['details'][0][0]['id'] = 90000000;   # 非存在

        $response = $this->json('PUT', self::API_URL . $basicKnowledgeId, $param);
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
     * 異常：基礎知識詳細が削除済
     *
     * @return void
     */
    public function test_異常：基礎知識詳細が削除済()
    {
        $basicKnowledgeId = 100101;
        $param = $this->baseParam;
        $param['details'][0][0]['id'] = 10010109;   # 削除済

        $response = $this->json('PUT', self::API_URL . $basicKnowledgeId, $param);
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
     * 異常：基礎知識詳細が紐付く基礎知識が異なる
     *
     * @return void
     */
    public function test_異常：基礎知識詳細が紐付く基礎知識が異なる()
    {
        $basicKnowledgeId = 100101;
        $param = $this->baseParam;
        $param['details'][0][0]['id'] = 10010201;   # 基礎知識ID=100102

        $response = $this->json('PUT', self::API_URL . $basicKnowledgeId, $param);
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
     * 異常：基礎知識画像が非存在
     *
     * @return void
     */
    public function test_異常：基礎知識画像が非存在()
    {
        $basicKnowledgeId = 100101;
        $param = $this->baseParam;
        $param['images'][0]['id'] = 90000000;   # 非存在

        $response = $this->json('PUT', self::API_URL . $basicKnowledgeId, $param);
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
     * 異常：基礎知識画像が削除済
     *
     * @return void
     */
    public function test_異常：基礎知識画像が削除済()
    {
        $basicKnowledgeId = 100101;
        $param = $this->baseParam;
        $param['images'][0]['id'] = 10010109;   # 削除済

        $response = $this->json('PUT', self::API_URL . $basicKnowledgeId, $param);
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
     * 異常：基礎知識画像が紐付く基礎知識が異なる
     *
     * @return void
     */
    public function test_異常：基礎知識画像が紐付く基礎知識が異なる()
    {
        $basicKnowledgeId = 100101;
        $param = $this->baseParam;
        $param['images'][0]['id'] = 10010201;   # 基礎知識ID=100102

        $response = $this->json('PUT', self::API_URL . $basicKnowledgeId, $param);
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
     * 異常：基礎知識URLが非存在
     *
     * @return void
     */
    public function test_異常：基礎知識URLが非存在()
    {
        $basicKnowledgeId = 100101;
        $param = $this->baseParam;
        $param['urls'][0]['id'] = 90000000;   # 非存在

        $response = $this->json('PUT', self::API_URL . $basicKnowledgeId, $param);
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
     * 異常：基礎知識URLが削除済
     *
     * @return void
     */
    public function test_異常：基礎知識URLが削除済()
    {
        $basicKnowledgeId = 100101;
        $param = $this->baseParam;
        $param['urls'][0]['id'] = 10010109;   # 削除済

        $response = $this->json('PUT', self::API_URL . $basicKnowledgeId, $param);
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
     * 異常：基礎知識URLが紐付く基礎知識が異なる
     *
     * @return void
     */
    public function test_異常：基礎知識URLが紐付く基礎知識が異なる()
    {
        $basicKnowledgeId = 100101;
        $param = $this->baseParam;
        $param['urls'][0]['id'] = 10010201;   # 基礎知識ID=100102

        $response = $this->json('PUT', self::API_URL . $basicKnowledgeId, $param);
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
}
