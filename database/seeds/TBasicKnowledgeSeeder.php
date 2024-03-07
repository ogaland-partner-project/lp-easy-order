<?php

use Illuminate\Database\Seeder;
use App\Models\TBasicKnowledge;

class TBasicKnowledgeSeeder extends Seeder
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
                'id' => 100101,
                'lp_order_id' => 1001,
                'question' => '尾賀太郎',
                'others' => '尾賀次郎',
            ],
            [
                'id' => 200101,
                'lp_order_id' => 2001,
                'question' => '蘭土太郎',
                'others' => '蘭土次郎',
            ],
        ])->each(function ($row) {
            $row = array_merge($row, [
                'created_pg' => 'TBasicKnowledgeSeeder',
                'updated_pg' => 'TBasicKnowledgeSeeder',
            ]);
            factory(TBasicKnowledge::class)->create($row);
        });
    }
}
