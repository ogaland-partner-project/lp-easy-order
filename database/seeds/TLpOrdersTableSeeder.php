<?php

namespace Database\Seeds;

use Illuminate\Database\Seeder;
use App\Models\TLpOrder;

class TLpOrderTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $model = TLpOrder::create([
            'product_name' => 'サンプル１',
            'product_code' => '1111',
            'description' => 'サンプル詳細01',
            'status' => 1,
            'requirement_flag' => 1,
        ]);
        $model = TLpOrder:create([
            'product_name' => 'サンプル２',
            'product_code' => '2222',
            'description' => 'サンプル詳細02',
            'status' => 0,
            'requirement_flag' => 0,
        ]);
    }

}
