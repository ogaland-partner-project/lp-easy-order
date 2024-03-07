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

class CreateTest extends TestCase
{
    use RefreshDatabase;

    const API_URL = '/api/lp_easy_order/basic_knowledge';

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

        $response = $this->json('POST', self::API_URL, $param);
        $response->assertStatus(200)
            ->assertJson([
                'dataArray' => '',
                'normalMessage' => CommonMsg::MSG_ID_000001,
                'errorMessage' => '',
            ]);

        // 基礎知識
        $basicKnowledge = TBasicKnowledge::orderBy('id')->get()->last();
        $expect = $param;
        $this->assertArrayEquals($expect, $basicKnowledge, ['id', 'details', 'images', 'urls']);

        // 基礎知識詳細
        $basicKnowledgeDetailGroups = TBasicKnowledgeDetail::where('basic_knowledge_id', $basicKnowledge->id)
            ->orderBy('col')->orderBy('sort_order')->get()->groupBy('col');
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
            });
            $colIndex++;
        });

        // 基礎知識画像
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
            // - 画像ファイルの生成チェック
            $this->assertTrue(Storage::exists($imagePath));
        });

        // 基礎知識URL
        $basicKnowledgeUrls = TBasicKnowledgeUrl::where('basic_knowledge_id', $basicKnowledge->id)
            ->orderBy('id')->get();
        $basicKnowledgeUrls->each(function ($basicKnowledgeImage, $i) use ($basicKnowledge, $param) {
            $expect = array_merge($param['urls'][$i], [
                'basic_knowledge_id' => $basicKnowledge->id,
            ]);
            $this->assertArrayEquals($expect, $basicKnowledgeImage, ['id']);
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
     * 異常：詳細情報が配列でない
     *
     * @return void
     */
    public function test_異常：詳細情報が配列でない()
    {
        $param = $this->baseParam;
        $param['details'] = '';

        $response = $this->json('POST', self::API_URL, $param);
        $response->assertStatus(422)
            ->assertJson([
                'dataArray' => [],
                'normalMessage' => '',
                'errorMessage' => '詳細情報は配列でなくてはなりません。',
            ]);
    }

    /**
     * 異常：画像情報が配列でない
     *
     * @return void
     */
    public function test_異常：画像情報が配列でない()
    {
        $param = $this->baseParam;
        $param['images'] = '';

        $response = $this->json('POST', self::API_URL, $param);
        $response->assertStatus(422)
            ->assertJson([
                'dataArray' => [],
                'normalMessage' => '',
                'errorMessage' => '画像情報は配列でなくてはなりません。',
            ]);
    }

    /**
     * 異常：URL情報が配列でない
     *
     * @return void
     */
    public function test_異常：URL情報が配列でない()
    {
        $param = $this->baseParam;
        $param['urls'] = '';

        $response = $this->json('POST', self::API_URL, $param);
        $response->assertStatus(422)
            ->assertJson([
                'dataArray' => [],
                'normalMessage' => '',
                'errorMessage' => 'URL情報は配列でなくてはなりません。',
            ]);
    }
}
