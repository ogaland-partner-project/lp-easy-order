<?php

use Illuminate\Database\Seeder;
use App\Models\TRawMaterial;

class TRawMaterialSeeder extends Seeder
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
                'item_karte_id' => 100101,
                'raw_material_name' => 'アーモンド',
                'production_area' => 'アメリカ',
                'raw_material_detail' => '大きい',
                'production_area_publish_flag' => 1,
                'photo_material_flag' => 1,
                'certificate_flag' => 1,
                'coverage_content_flag' => 1,
                'document_path' => 'storage/documents/Almond.doc',
            ],
            [
                'id' => 20010101,
                'item_karte_id' => 200101,
                'raw_material_name' => 'よもぎ',
                'production_area' => '日本',
                'raw_material_detail' => '無添加',
                'production_area_publish_flag' => 0,
                'photo_material_flag' => 0,
                'certificate_flag' => 0,
                'coverage_content_flag' => 0,
                'document_path' => 'storage/documents/Yomogicha.doc',
            ],
        ])->each(function ($row) {
            $row = array_merge($row, [
                'created_pg' => 'TRawMaterialSeeder',
                'updated_pg' => 'TRawMaterialSeeder',
            ]);
            factory(TRawMaterial::class)->create($row);
        });
    }
}
