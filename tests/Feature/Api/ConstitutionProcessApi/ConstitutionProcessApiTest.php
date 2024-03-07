<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Database\Seeds\TLpOrderTableSeeder as TLpOrderTableSeeder;
use Database\Seeds\TConstitutionProcessTableSeeder as TConstitutionProcessTableSeeder;
use Database\Seeds\TConstitutionBlockTableSeeder as TConstitutionBlockTableSeeder;
use Database\Seeds\TConstitutionFixBlocksTableSeeder as TConstitutionFixBlocksTableSeeder;
use App\Models\TLpOrder;
use App\Models\TConstitutionProcess;
use App\Models\TConstitutionBlock;
use App\Models\TConstitutionFixBlocks;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Exception;

class ConstitutionProcessApiTest extends TestCase
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
        $this->seed(TConstitutionProcessTableSeeder::class);
        $this->seed(TConstitutionBlockTableSeeder::class);
        $this->seed(TConstitutionFixBlocksTableSeeder::class);

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

    /***** API\ConstitutionProcessApi@index *****/
    /**
     * 正常ケース
     * API\ConstitutionProcessApi@index
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
                    'concept_word' => 'コンセプト01',
                    'concept_catch' => 'キャッチ01',
                    'how_block' => 'ブロック内容01',
                    'constitution_blocks_list' =>  [
                        'id' => 1,
                        'constitution_process_id' => 1,
                        'block_detail' => 'ブロック01',
                        'sort_order' => 1,
                    ],
                    'constitution_fix_blocks_list' =>  [
                        'id' => 1,
                        'constitution_process_id' => 1,
                        'block_detail' => 'ブロックFix01',
                        'sort_order' => 1,
                    ]
                ],
            ],
            "normalMessage" => '',
            "errorMessage" => ''
        ]);
        $result = $this->call("GET", "/api/lp_easy_order/constitution_process/".$row['id']);
        $result->assertStatus(200);
        $this->assertSame($expect->getContent(), $result->getContent());
    }

    /***** API\ConstitutionProcessApi@create *****/
    /**
     * 正常ケース(1) 登録
     * API\ConstitutionProcessApi@create
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
            'concept_word' => 'コンセプト019',
            'concept_catch' => 'キャッチ019',
            'how_block' => 'ブロック内容019',
            'constitution_blocks_list' => [[
                'constitution_process_id' => 0,
                'block_detail' => 'ブロック019',
                'sort_order' => 1,
            ]],
            'constitution_fix_blocks_list' => [[
                'constitution_process_id' => 0,
                'block_detail' => 'ブロック019Fix',
                'sort_order' => 1,
            ]],
        ];
        $result = $this->post("/api/lp_easy_order/constitution_process", $datas);
        $result->assertStatus(200);
        $this->assertSame($expect->getContent(), $result->getContent());
    }
    
    /***** API\ConstitutionProcessApi@create *****/
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
            "errorMessage" => "The lp order id field is required."
        ]);
        $row = TLpOrder::where("product_name", "サンプル１")->first();
        $datas = [
            'lp_order_id' => "",
            'concept_word' => 'コンセプト020',
            'concept_catch' => 'キャッチ020',
            'how_block' => 'ブロック内容020',
            'constitution_blocks_list' => [[
                'constitution_process_id' => 0,
                'block_detail' => 'ブロック020',
                'sort_order' => 2,
            ]],
            'constitution_fix_blocks_list' => [[
                'constitution_process_id' => 0,
                'block_detail' => 'ブロック020Fix',
                'sort_order' => 2,
            ]],
        ];
        $result = $this->post("/api/lp_easy_order/constitution_process", $datas);
        $result->assertStatus(422);
        $this->assertSame($expect->getContent(), $result->getContent());
    }
    
    /***** API\ConstitutionProcessApi@update *****/
    /**
     * 正常ケース(1) 更新
     * API\ConstitutionProcessApi@update
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
        $row = TLpOrder:where("product_name", "サンプル１")->first();
        $datas = [
            'lp_order_id' => $row['id'],
            'concept_word' => 'コンセプト119',
            'concept_catch' => 'キャッチ119',
            'how_block' => 'ブロック内容119',
            'constitution_blocks_list' => [[
                'constitution_process_id' => 0,
                'block_detail' => 'ブロック119',
                'sort_order' => 1,
            ]],
            'constitution_fix_blocks_list' => [[
                'constitution_process_id' => 0,
                'block_detail' => 'ブロック119Fix',
                'sort_order' => 1,
            ]],
        ];
        $result = $this->put("/api/lp_easy_order/constitution_process/".$row['id'], $datas);
        $result->assertStatus(200);
        $this->assertSame($expect->getContent(), $result->getContent());
    }

    /**
     * 異常ケース(1) 更新　更新値の不正(型不正) 
     * API\ConstitutionProcessApi@update
     * @test
     * @return void
     */
    public function testUpdate_02()
    {
        $expect = response()->json([
            "dataArray" => [],
            "normalMessage" => "",
            "errorMessage" => "The lp order id field is required."
        ]);
        $row = TLpOrder::where("product_name", "サンプル２")->first();
        $datas = [
            'lp_order_id' => "",
            'concept_word' => 'コンセプト119',
            'concept_catch' => 'キャッチ119',
            'how_block' => 'ブロック内容119',
            'constitution_blocks_list' => [[
                'constitution_process_id' => 0,
                'block_detail' => 'ブロック119',
                'sort_order' => 1,
            ]],
            'constitution_fix_blocks_list' => [[
                'constitution_process_id' => 0,
                'block_detail' => 'ブロック119Fix',
                'sort_order' => 1,
            ]],
        ];
        $result = $this->put("/api/lp_easy_order/constitution_process/".$row['id'], $datas);
        $result->assertStatus(422);
        $this->assertSame($expect->getContent(), $result->getContent());
    }

    /***** API\ConstitutionProcessApi@copy *****/
    /**
     * 正常ケース(1) 更新
     * API\HomeConstitutionProcessApi@copy
     * @test
     * @return void
     */
    public function testCopy_01()
    {
        $expect = response()->json([
            "dataArray" => [],
            "normalMessage" => "登録完了しました。",
            "errorMessage" => ""
        ]);
        $row_1 = TLpOrder::where("product_name", "サンプル１")->first();
        $row_2 = TLpOrder::where("product_name", "サンプル２")->first();
        $datas = [
            'lp_order_id' => $row_1['id'],
            'other_lp_order_id' => $row_2['id'],
        ];
        $result = $this->post("/api/lp_easy_order/constitution_process/copy", $datas);
        $result->assertStatus(200);
        $this->assertSame($expect->getContent(), $result->getContent());
    }

    /**
     * 異常ケース(1) 更新　更新値の不正(型不正) 
     * API\ConstitutionProcessApi@copy
     * @test
     * @return void
     */
    public function testCopy_02()
    {
        $expect = response()->json([
            "dataArray" => [],
            "normalMessage" => "",
            "errorMessage" => "The lp order id field is required."
        ]);
        $row_2 = TLpOrder::where("product_name", "サンプル２")->first();
        $datas = [
            'lp_order_id' => "",
            'other_lp_order_id' => $row_2['id'],
        ];
        $result = $this->post("/api/lp_easy_order/constitution_process/copy", $datas);
        $result->assertStatus(422);
        $this->assertSame($expect->getContent(), $result->getContent());
    }


}
