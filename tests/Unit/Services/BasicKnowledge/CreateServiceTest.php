<?php

namespace Tests\Unit\Services\BasicKnowledge;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use App\Common\CommonMsg;
use Exception;
use App\Services\BasicKnowledge\CreateService;
use App\Models\TLpOrder;
use App\Models\TBasicKnowledge;
use App\Models\TBasicKnowledgeDetail;
use App\Models\TBasicKnowledgeImage;
use App\Models\TBasicKnowledgeUrl;
use Illuminate\Support\Str;

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

        // 画像ファイルの削除
        Storage::deleteDirectory('lp_order');

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
                'id' => 100100,
                'lp_order_id' => 1001,
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
            [
                'basic_knowledge_id' => 100100,
                'col' => 10,
                'sort_order' => 1,
            ],
            [
                'basic_knowledge_id' => 100100,
                'col' => 10,
                'sort_order' => 2,
            ],
            [
                'basic_knowledge_id' => 100100,
                'col' => 20,
                'sort_order' => 1,
            ],
            [
                'basic_knowledge_id' => 100100,
                'col' => 20,
                'sort_order' => 2,
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
            [
                'id' => 10010001,
                'basic_knowledge_id' => 100100,
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
        
        // t_basic_knowledge_urlテーブル
        collect([
            [
                'id' => 10010001,
                'basic_knowledge_id' => 100100,
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
            'lp_order_id' => 1001,
            'details' => [
                [
                    [
                        'id' => null,
                        'title' => 'タイトル_1001_1_1',
                        'detail' => '詳細_1001_1_1',
                    ],
                    [
                        'id' => null,
                        'title' => 'タイトル_1001_1_2',
                        'detail' => '詳細_1001_1_2',
                    ],
                ],
                [
                    [
                        'id' => null,
                        'title' => 'タイトル_1001_2_1',
                        'detail' => '詳細_1001_2_1',
                    ],
                    [
                        'id' => null,
                        'title' => 'タイトル_1001_2_2',
                        'detail' => '詳細_1001_2_2',
                    ],
                ],
            ],
            'images' => [
                [
                    'id' => null,
                    'image_path' => '',
                    'file' => $this->getImageFileString('tests/resources/BasicKnowledge/image.jpg'),
                    'image_memo' => '画像メモ_1001_1',
                ],
                [
                    'id' => null,
                    'image_path' => '',
                    'file' => $this->getImageFileString('tests/resources/BasicKnowledge/image.png'),
                    'image_memo' => '画像メモ_1001_2',
                ],
            ],
            'urls' => [
                [
                    'id' => null,
                    'url' => 'https://example.com/1001_1',
                ],
                [
                    'id' => null,
                    'url' => 'https://example.com/1001_2',
                ],
            ],
            'question' => '疑問点_1001',
            'others' => 'その他_1001',
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
        $formerBasicKnowledgesCount = TBasicKnowledge::count();
        $formerBasicKnowledgeDetailsCount = TBasicKnowledgeDetail::count();
        $formerBasicKnowledgeImagesCount = TBasicKnowledgeImage::count();
        $formerBasicKnowledgeUrlsCount = TBasicKnowledgeUrl::count();

        $basicKnowledgeId = $this->service->execCreate($param);

        // 基礎知識
        // - 登録件数のチェック
        $this->assertEquals($formerBasicKnowledgesCount + 1, TBasicKnowledge::count());
        $basicKnowledge = TBasicKnowledge::orderBy('id')->get()->last();
        $this->assertEquals($basicKnowledgeId, $basicKnowledge->id);
        $expect = $param;
        $this->assertArrayEquals($expect, $basicKnowledge, ['id', 'details', 'images', 'urls']);
        $this->assertNotNull($basicKnowledge->created_pg);
        $this->assertNotNull($basicKnowledge->updated_pg);

        // 基礎知識詳細
        $basicKnowledgeDetails = TBasicKnowledgeDetail::where('basic_knowledge_id', $basicKnowledge->id)
            ->orderBy('col')->orderBy('sort_order')->get();
        // - 登録件数のチェック
        $detailRows = collect($param['details'])->reduce(function($detaiRows, $rows) {
            return $detaiRows->merge(collect($rows));
        }, collect([]));
        $this->assertEquals($formerBasicKnowledgeDetailsCount + $detailRows->count(), TBasicKnowledgeDetail::count());
        // - 登録内容チェック
        $basicKnowledgeDetailGroups = $basicKnowledgeDetails->groupBy('col');
        $colIndex = 0;
        $basicKnowledgeDetailGroups->each(function ($details) use ($basicKnowledge, $param, &$colIndex) {
            $detailsParam = $param['details'][$colIndex];
            $this->assertCount(count($detailsParam), $details);
            $details->each(function ($detail, $i) use ($colIndex, $basicKnowledge, $detailsParam) {
                $expect = array_merge($detailsParam[$i], [
                    'basic_knowledge_id' => $basicKnowledge->id,
                    'col' => $colIndex,
                    'sort_order' => $i,
                ]);
                $this->assertArrayEquals($expect, $detail, ['id']);
                $this->assertNotNull($detail->created_pg);
                $this->assertNotNull($detail->updated_pg);
            });
            $colIndex++;
        });

        // 基礎知識画像
        // - 登録件数のチェック
        $this->assertEquals($formerBasicKnowledgeImagesCount + count($param['images']), TBasicKnowledgeImage::count());
        // - 登録内容チェック
        $basicKnowledgeImages = TBasicKnowledgeImage::where('basic_knowledge_id', $basicKnowledge->id)
            ->orderBy('id')->get();
        $basicKnowledgeImages->each(function ($basicKnowledgeImage, $i) use ($basicKnowledge, $param) {
            // - 画像ファイルパス
            preg_match('/^data:image\/(\w+);base64,.+$/', $param['images'][$i]['file'], $matches);
            $imagePath = sprintf(
                'lp_order/%d/basicknowledge/%d.%s',
                $basicKnowledge->lp_order_id,
                $basicKnowledgeImage->id,
                $matches[1]
            );
            $expect = array_merge($param['images'][$i], [
                'basic_knowledge_id' => $basicKnowledge->id,
                'image_path' => '/storage/' . $imagePath,
            ]);
            $this->assertArrayEquals($expect, $basicKnowledgeImage, ['id', 'file']);
            $this->assertNotNull($basicKnowledgeImage->created_pg);
            $this->assertNotNull($basicKnowledgeImage->updated_pg);
            // - 画像ファイルの生成チェック
            $this->assertTrue(Storage::exists($imagePath));
        });

        // 基礎知識URL
        $basicKnowledgeUrls = TBasicKnowledgeUrl::where('basic_knowledge_id', $basicKnowledge->id)
            ->orderBy('id')->get();
        // - 登録件数のチェック
        $this->assertEquals($formerBasicKnowledgeUrlsCount + count($param['urls']), TBasicKnowledgeUrl::count());
        // - 登録内容チェック
        $basicKnowledgeUrls->each(function ($basicKnowledgeUrl, $i) use ($basicKnowledge, $param) {
            $expect = array_merge($param['urls'][$i], [
                'basic_knowledge_id' => $basicKnowledge->id,
            ]);
            $this->assertArrayEquals($expect, $basicKnowledgeUrl, ['id']);
            $this->assertNotNull($basicKnowledgeUrl->created_pg);
            $this->assertNotNull($basicKnowledgeUrl->updated_pg);
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
