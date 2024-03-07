<?php

use Illuminate\Database\Seeder;
use App\Models\TLpOrder;

class TLpOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect([
            [
                'id' => 1001,
                'product_name' => 'ミックスナッツ',
                'product_code' => 'MN001',
                'description' => '楽天みつぎ工作のミックスナッツ',
                'status' => 0,
                'requirement_flag' => 1,
            ],
            [
                'id' => 2001,
                'product_name' => 'よもぎ茶',
                'product_code' => 'MN002',
                'description' => '楽天みつぎ工作のよもぎ茶',
                'status' => 0,
                'requirement_flag' => 1,
            ],
        ])->each(function ($row) {
            $row = array_merge($row, [
                'created_pg' => 'TLpOrderSeeder',
                'updated_pg' => 'TLpOrderSeeder',
            ]);
            factory(TLpOrder::class)->create($row);
        });
    }
}
