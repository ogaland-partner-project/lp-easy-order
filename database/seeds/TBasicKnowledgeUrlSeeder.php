<?php

use Illuminate\Database\Seeder;
use App\Models\TBasicKnowledgeUrl;

class TBasicKnowledgeUrlSeeder extends Seeder
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
            ],
            [
                'id' => 10010102,
                'basic_knowledge_id' => 100101,
            ],
            [
                'id' => 20010101,
                'basic_knowledge_id' => 200101,
            ],
            [
                'id' => 20010102,
                'basic_knowledge_id' => 200101,
            ],
        ])->each(function ($row) {
            $row = array_merge($row, [
                'url' => 'https://example.net/page/' . $row['id'],
                'created_pg' => 'TBasicKnowledgeUrlSeeder',
                'updated_pg' => 'TBasicKnowledgeUrlSeeder',
            ]);
            factory(TBasicKnowledgeUrl::class)->create($row);
        });
    }
}
