<?php

use Illuminate\Database\Seeder;
use App\Models\TBasicKnowledgeImage;

class TBasicKnowledgeImageSeeder extends Seeder
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
                'image_memo' => '画像1-A',
            ],
            [
                'id' => 10010102,
                'basic_knowledge_id' => 100101,
                'image_memo' => '画像1-B',
            ],
            [
                'id' => 20010101,
                'basic_knowledge_id' => 200101,
                'image_memo' => '画像2-A',
            ],
            [
                'id' => 20010102,
                'basic_knowledge_id' => 200101,
                'image_memo' => '画像2-B',
            ],
        ])->each(function ($row) {
            $row = array_merge($row, [
              'image_path' => sprintf(
                  '/storage/lp_order/%d/basicknowledge/%d.png',
                  floor($row['id'] / 10000),
                  $row['id']
              ),
              'created_pg' => 'TBasicKnowledgeImageSeeder',
              'updated_pg' => 'TBasicKnowledgeImageSeeder',
            ]);
          factory(TBasicKnowledgeImage::class)->create($row);
        });
    }
}
