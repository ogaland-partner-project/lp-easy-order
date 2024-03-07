<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Database\Seeds\TLpOrderTableSeeder as TLpOrderTableSeeder;
use Database\Seeds\TLevelSelectTableSeeder as TLevelSelectTableSeeder;
use Database\Seeds\TLevelSelectLpBlockTableSeeder as TLevelSelectLpBlockTableSeeder;
use App\Models\TLpOrder;
use App\Models\TLevelSelect
use App\Models\TLevelSelectLpBlock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Exception;

class LevelSelectApiTest extends TestCase
{

    use RefreshDatabase;
    private static $isSetUp = false;


    /**
     * 共通初期処理
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // テストデータ生成
        $this->seed(TLpOrderTableSeeder::class);
        $this->seed(TLevelSelectTableSeeder::class);
        $this->seed(TLevelSelectLpBlockableSeeder::class);

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

    /***** API\HomeApi@index *****/
    /**
     * 正常ケース
     * API\HomeApi@index
     * @test
     * @return void
     */
    public function testIndex_01()
    {
        $row = TLpOrder::where("product_name", "サンプル１")->first();
        $expect = response()->json([
            "dataArray" => [
                [
                    'id' => 1,
                    'lp_order_id' => $row['id'],
                    'promoter' => 'サンプル花子01',
                    'configurator' => 'サンプル太郎01',
                    'designer' => 'サンプル花美01',
                    'level' => 1,
                    'purpose' => 'ユニバーサルデザイン',
                    'point1' => '清潔感',
                    'point2' => 'おしゃれ',
                    'point3' => 'すばらしい',
                    'taste' => 'みたいなデザイン',
                    't_level_select_lp_blocks' =>  [
                        'id' => 1,
                        'level_select_id' => 1,
                        'block_detail' => '商品画像を大きく目立たせる',
                        'sort_order' => 1,
                    ]
                ],
            ],
            "normalMessage" => '',
            "errorMessage" => ''
        ]);
        $result = $this->call("GET", "/api/lp_easy_order/level_select/".$row['id']);
        $result->assertStatus(200);
        $this->assertSame($expect->getContent(), $result->getContent());
    }

    /***** API\HomeApi@create *****/
    /**
     * 正常ケース(1) 登録
     * API\HomeApi@create
     * @test
     * @return void
     */
    public function testCreate_01()
    {
        $expect = response()->json([
            "dataArray" => [],
            "normalMessage" => "登録完了しました。",
            "errorMessage" => ""
        ]);
        $row = TLpOrder::where("product_name", "サンプル１")->first();
        $datas = [
            'lp_order_id' => $row['id'],
            'promoter' => 'サンプル花子03',
            'configurator' => 'サンプル太郎03',
            'designer' => 'サンプル花美03',
            'level' => 3,
            'purpose' => 'ユニバーサルデザイン',
            'point1' => '清潔感',
            'point2' => 'おしゃれ',
            'point3' => 'すばらしい',
            'taste' => 'みたいなデザイン',
            'lp_block' => [
                [
                    'level_select_id' =>  0,
                    'block_detail' => '商品画像を大きく目立たせる',
                    'sort_order' => 0
                ],
                [
                    'level_select_id' =>  0,
                    'block_detail' => '商品の細かい仕様をわかりやすく',
                    'sort_order' => 0
                ],
            ]
        ];
        $result = $this->post("/api/lp_easy_order/level_select", $datas);
        $result->assertStatus(200);
        $this->assertSame($expect->getContent(), $result->getContent());
    }
    
        /***** API\HomeApi@create *****/
    /**
     * 異常ケース(1) 登録　登録値の不正(型不正) 
     * API\HomeApi@create
     * @test
     * @return void
     */
    public function testCreate_02()
    {
        $expect = response()->json([
            "dataArray" => [],
            "normalMessage" => "",
            "errorMessage" => "The level must be an integer."
        ]);
        $row = TLpOrder::where("product_name", "サンプル１")->first();
        $datas = [
            'lp_order_id' => $row['id'],
            'promoter' => 'サンプル花子03',
            'configurator' => 'サンプル太郎03',
            'designer' => 'サンプル花美03',
            'level' => 'xxxx',
            'purpose' => 'ユニバーサルデザイン',
            'point1' => '清潔感',
            'point2' => 'おしゃれ',
            'point3' => 'すばらしい',
            'taste' => 'みたいなデザイン',
            'lp_block' => [
                [
                    'level_select_id' =>  0,
                    'block_detail' => '商品画像を大きく目立たせる',
                    'sort_order' => 0
                ],
                [
                    'level_select_id' =>  0,
                    'block_detail' => '商品の細かい仕様をわかりやすく',
                    'sort_order' => 0
                ],
            ]
        ];
        $result = $this->post("/api/lp_easy_order/level_select", $datas);
        $result->assertStatus(422);
        $this->assertSame($expect->getContent(), $result->getContent());
    }

    /***** API\HomeApi@update *****/
    /**
     * 正常ケース(1) 更新
     * API\HomeApi@update
     * @test
     * @return void
     */
    public function testUpdate_01()
    {
        $expect = response()->json([
            "dataArray" => [],
            "normalMessage" => "更新完了しました。",
            "errorMessage" => ""
        ]);
        $row = TLevelSelect::where("promoter", "サンプル花子01")->first();
        $row_bl = TLevelSelectLpBlock:where("level_select_id", $row['id'])->first();
        $datas = [
            'lp_order_id' => $row['lp_order_id'],
            'promoter' => 'サンプル花子04',
            'configurator' => 'サンプル太郎04',
            'designer' => 'サンプル花美04',
            'level' => '4',
            'purpose' => 'ユニバーサルデザイン',
            'point1' => '清潔感',
            'point2' => 'おしゃれ',
            'point3' => 'すばらしい',
            'taste' => 'みたいなデザイン',
            'lp_block' => [
                [
                    'level_select_id' =>  0,
                    'block_detail' => '更新:商品画像を大きく目立たせる',
                    'sort_order' => 0
                ],
                [
                    'id' => $row_bl['id'],
                    'level_select_id' =>  '0',
                    'block_detail' => '更新:商品の細かい仕様をわかりやすく',
                    'sort_order' => 0
                ],
            ]
        ];
        $result = $this->put("/api/lp_easy_order/level_select/".$row['id'], $datas);
        $result->assertStatus(200);
        $this->assertSame($expect->getContent(), $result->getContent());
    }

    /**
     * 異常ケース(1) 更新　更新値の不正(型不正) 
     * API\HomeApi@update
     * @test
     * @return void
     */
    public function testUpdate_02()
    {
        $expect = response()->json([
            "dataArray" => [],
            "normalMessage" => "",
            "errorMessage" => "The level must be an integer."
        ]);
        $row = TLevelSelect::where("promoter", "サンプル花子01")->first();
        $row_bl = TLevelSelectLpBlock::where("level_select_id", $row['id'])->first();
        $datas = [
            'lp_order_id' => $row['lp_order_id'],
            'promoter' => 'サンプル花子04',
            'configurator' => 'サンプル太郎04',
            'designer' => 'サンプル花美04',
            'level' => 'xxxx',
            'purpose' => 'ユニバーサルデザイン',
            'point1' => '清潔感',
            'point2' => 'おしゃれ',
            'point3' => 'すばらしい',
            'taste' => 'みたいなデザイン',
            'lp_block' => [
                [
                    'level_select_id' =>  0,
                    'block_detail' => '更新:商品画像を大きく目立たせる',
                    'sort_order' => 0
                ],
                [
                    'id' => $row_bl['id'],
                    'level_select_id' =>  '0',
                    'block_detail' => '更新:商品の細かい仕様をわかりやすく',
                    'sort_order' => 0
                ],
            ]
        ];
        $result = $this->put("/api/lp_easy_order/level_select/".$row['id'], $datas);
        $result->assertStatus(422);
        $this->assertSame($expect->getContent(), $result->getContent());
    }

}
