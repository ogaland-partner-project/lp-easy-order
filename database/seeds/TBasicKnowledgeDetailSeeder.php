<?php

use Illuminate\Database\Seeder;
use App\Models\TBasicKnowledgeDetail;

class TBasicKnowledgeDetailSeeder extends Seeder
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
                'id' => 10010101,
                'basic_knowledge_id' => 100101,
                'title' => '商品/成分名',
                'detail' => 'ポン酢',
                'col' => 0,
                'sort_order' => 0,
            ],
            [
                'id' => 10010102,
                'basic_knowledge_id' => 100101,
                'title' => '一般的な認知度',
                'detail' => '高',
                'col' => 0,
                'sort_order' => 1,
            ],
            [
                'id' => 10010103,
                'basic_knowledge_id' => 100101,
                'title' => '商品/成分名',
                'detail' => '蜂蜜',
                'col' => 1,
                'sort_order' => 0,
            ],
            [
                'id' => 10010104,
                'basic_knowledge_id' => 100101,
                'title' => '一般的な認知度',
                'detail' => '高',
                'col' => 1,
                'sort_order' => 1,
            ],
            [
                'id' => 20010101,
                'basic_knowledge_id' => 200101,
                'title' => '商品/成分名',
                'detail' => 'よもぎ',
                'col' => 0,
                'sort_order' => 0,
            ],
            [
                'id' => 20010102,
                'basic_knowledge_id' => 200101,
                'title' => '一般的な認知度',
                'detail' => '中',
                'col' => 0,
                'sort_order' => 1,
            ],
            [
                'id' => 20010103,
                'basic_knowledge_id' => 200101,
                'title' => '商品/成分名',
                'detail' => 'βカロテン',
                'col' => 1,
                'sort_order' => 0,
            ],
            [
                'id' => 20010104,
                'basic_knowledge_id' => 200101,
                'title' => '一般的な認知度',
                'detail' => '低',
                'col' => 1,
                'sort_order' => 1,
            ],
        ])->each(function ($row) {
            $row = array_merge($row, [
              'created_pg' => 'TBasicKnowledgeDetailSeeder',
              'updated_pg' => 'TBasicKnowledgeDetailSeeder',
            ]);
            factory(TBasicKnowledgeDetail::class)->create($row);
        });
    }
}
