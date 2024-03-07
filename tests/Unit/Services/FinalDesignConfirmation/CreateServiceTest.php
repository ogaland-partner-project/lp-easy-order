<?php

namespace Tests\Unit\Services\FinalDesignConfirmation;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use App\Common\CommonMsg;
use Exception;
use App\Services\FinalDesignConfirmation\CreateService;
use App\Models\TFinalDesignConfirmation;
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
                'id' => 1,
                'status' => 0,
                'requirement_flag' => 0,
            ],
            [
                'id' => 2,
                'status' => 0,
                'requirement_flag' => 0,
            ],
            [
                'id' => 3,
                'status' => 0,
                'requirement_flag' => 0,
            ],
            [
                'id' => 4,
                'status' => 0,
                'requirement_flag' => 0,
            ],
            [
                'id' => 5,
                'status' => 0,
                'requirement_flag' => 0,
            ],
            [
                'id' => 6,
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

        // t_final_design_confirmations
        collect([
            [
                'id' => 1,
                'lp_order_id' => 1,
                'final_image_path' => '/storage/lp_order/1/finaldesignconfirmation/1_origenal.jpg',
                'final_image_fix_path' => '/storage/lp_order/1/finaldesignconfirmation/1_fix.jpg',
            ],
            [
                'id' => 2,
                'lp_order_id' => 1,
                'final_image_path' => '/storage/lp_order/1/finaldesignconfirmation/2_origenal.jpg',
                'final_image_fix_path' => '/storage/lp_order/1/finaldesignconfirmation/2_fix.jpg',
            ],
            [
                'id' => 3,
                'lp_order_id' => 1,
                'final_image_path' => '/storage/lp_order/1/finaldesignconfirmation/3_origenal.jpg',
                'final_image_fix_path' => '/storage/lp_order/1/finaldesignconfirmation/3_fix.jpg',
            ],
            [
                'id' => 4,
                'lp_order_id' => 1,
                'final_image_path' => '/storage/lp_order/1/finaldesignconfirmation/4_origenal.jpg',
                'final_image_fix_path' => '/storage/lp_order/1/finaldesignconfirmation/4_fix.jpg',
            ],
            [
                'id' => 5,
                'lp_order_id' => 1,
                'final_image_path' => '/storage/lp_order/1/finaldesignconfirmation/5_origenal.jpg',
                'final_image_fix_path' => '/storage/lp_order/1/finaldesignconfirmation/5_fix.jpg',
            ],
            [
                'id' => 6,
                'lp_order_id' => 2,
                'final_image_path' => '/storage/lp_order/1/finaldesignconfirmation/6_origenal.jpg',
                'final_image_fix_path' => '/storage/lp_order/1/finaldesignconfirmation/6_fix.jpg',
            ],
            [
                'id' => 7,
                'lp_order_id' => 3,
                'deleted_at' => now(),      // 削除済
            ],
        ])->each(function ($row) {
            factory(TFinalDesignConfirmation::class)->create(
                array_merge($row, [
                    'fix_detail' => '<P>ここにHTML記法で修正必要内容が入る</P>',
                    're_fix_memo' => '<P>ここにHTML記法で修正必要内容が入る</P>',
                    'created_pg' => 'init',
                    'created_at'=> now()
                ])
            );
        });

        // 基本パラメータ（リクエスト）
        $this->baseParam = [
            'design_parts' => [
                [
                    'lp_order_id' => 1,
                    'final_image_path' => '',
                    'final_image_file' => $this->getImageFileString('tests/resources/BasicKnowledge/image.jpg'),
                    'fix_detail' => '<p>24ここにHTML記法で修正必要内容が入る</p>',
                    'final_image_fix' => $this->getImageFileString('tests/resources/BasicKnowledge/image.jpg'),
                    'final_image_fix_file' => '24fix.jpg',
                    're_fix_memo' => '<p>24ここにHTML記法で再修正・メモが入る</p>'
                ],
                [
                    'lp_order_id'=> 2,
                    'final_image_path'=> '',
                    'final_image_file'=> $this->getImageFileString('tests/resources/BasicKnowledge/image.png'),
                    'fix_detail'=> '<p>25ここにHTML記法で修正必要内容が入る</p>',
                    'final_image_fix'=> '',
                    'final_image_fix_file'=> $this->getImageFileString('tests/resources/BasicKnowledge/image.png'),,
                    're_fix_memo'=> '<p>25ここにHTML記法で再修正・メモが入る</p>'
                ]
            ]
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

        // 処理実行前のテーブル件数を取得
        $formerFinalDesignConfirmationCount = TFinalDesignConfirmation::count();

        // 検証対象のメソッド実行
        $finalDesignConfirmationId = $this->service->execCreate($param);

        // 最終デザイン確認

        // - 登録件数のチェック：DBに初期値として登録したデータ件数と、新規に登録したデータ件数が、テーブルの件数と一致すること。
        $this->assertEquals($formerFinalDesignConfirmationCount + $param->count(), TFinalDesignConfirmation::count());

        // $finalDesignConfirmation = TFinalDesignConfirmation::orderBy('id')->get()->last();

        // $basicKnowledgeImages = TBasicKnowledgeImage::where('basic_knowledge_id', $basicKnowledge->id)
        //     ->orderBy('id')->get();
        // $basicKnowledgeImages->each(function ($basicKnowledgeImage, $i) use ($basicKnowledge, $param) {
        //     // - 画像ファイルパス
        //     preg_match('/^data:image\/(\w+);base64,.+$/', $param['images'][$i]['file'], $matches);
        //     $imagePath = sprintf(
        //         'lp_order/%d/basicknowledge/%d.%s',
        //         $basicKnowledge->lp_order_id,
        //         $basicKnowledgeImage->id,
        //         $matches[1]
        //     );
        //     $expect = array_merge($param['images'][$i], [
        //         'basic_knowledge_id' => $basicKnowledge->id,
        //         'image_path' => '/storage/' . $imagePath,
        //     ]);
        //     $this->assertArrayEquals($expect, $basicKnowledgeImage, ['id', 'file']);
        //     $this->assertNotNull($basicKnowledgeImage->created_pg);
        //     $this->assertNotNull($basicKnowledgeImage->updated_pg);
        //     // - 画像ファイルの生成チェック
        //     $this->assertTrue(Storage::exists($imagePath));
        // });
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