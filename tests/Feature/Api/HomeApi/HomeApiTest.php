<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Database\Seeds\TLpOrderTableSeeder as TLpOrderTableSeeder;
use App\Models\TLpOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Exception;

class HomeApiTest extends TestCase
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
        $expect = response()->json([
            "dataArray" => [
                [
                    'id' => 1,
                    'product_name' => 'サンプル１',
                    'product_code' => '1111',
                    'description' => 'サンプル詳細01',
                    'status' => 1,
                    'requirement_flag' => 1,
                ],
                [
                    'id' => 2,
                    'product_name' => 'サンプル２',
                    'product_code' => '2222',
                    'description' => 'サンプル詳細02',
                    'status' => 0,
                    'requirement_flag' => 0,
                ],
            ],
            "normalMessage" => '',
            "errorMessage" => ''
        ]);
        
        $result = $this->call("GET", "/api/lp_easy_order/home");
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
        $datas = [
            'product_name' => 'サンプル３',
            'product_code' => '3333',
            'description' => 'サンプル詳細03',
            'status' => 1,
            'requirement_flag' => 1,
        ];
        $result = $this->post("/api/lp_easy_order/home", $datas);
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
            "errorMessage" => "The requirement flag must be an integer."
        ]);
        $datas = [
            'product_name' => 'サンプル３',
            'product_code' => '3333',
            'description' => 'サンプル詳細03',
            'status' => 1,
            'requirement_flag' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxx',
        ];
        $result = $this->post("/api/lp_easy_order/home", $datas);
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
        $datas = [
            'product_name' => 'サンプル２_更新',
            'product_code' => '2223',
            'description' => 'サンプル詳細02_更新',
            'status' => 0,
            'requirement_flag' => 0,
        ];
        $row = TLpOrder::where("product_name", "サンプル２")->first();
        $result = $this->put("/api/lp_easy_order/home/".$row['id'], $datas);
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
            "errorMessage" => "The requirement flag must be an integer."
        ]);
        $datas = [
            'product_name' => 'サンプル２_更新',
            'product_code' => '2223',
            'description' => 'サンプル詳細02_更新',
            'status' => 0,
            'requirement_flag' => 'xxxxxxxxxxx',
        ];
        $row = TLpOrder::where("product_name", "サンプル２")->first();
        $result = $this->put("/api/lp_easy_order/home/".$row['id'], $datas);
        $result->assertStatus(422);
        $this->assertSame($expect->getContent(), $result->getContent());
    }
    
    /***** API\HomeApi@delete *****/
    /**
     * 正常ケース(1) 削除
     * API\HomeApi@delete
     * @test
     * @return void
     */
    public function testDelete_01()
    {
        $expect = response()->json([
            "dataArray" => [],
            "normalMessage" => "削除完了しました。",
            "errorMessage" => ""
        ]);
        $row = TLpOrder::where("product_name", "サンプル１")->first();
        $result = $this->delete("/api/lp_easy_order/home/".$row['id']);
        $result->assertStatus(200);
        $this->assertSame($expect->getContent(), $result->getContent());
    }

}
