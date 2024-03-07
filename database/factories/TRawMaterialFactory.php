<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TRawMaterial;
use App\Models\TItemKarte;
use Faker\Generator as Faker;

$factory->define(TRawMaterial::class, function (Faker $faker) {
    return [
        'item_karte_id' => factory(TItemKarte::class),
        'raw_material_name' => $faker->word(),
        'production_area' => $faker->word(),
        'raw_material_detail' => $faker->sentence(),
        'production_area_publish_flag' => $faker->randomElement([0, 1]),
        'photo_material_flag' => $faker->randomElement([0, 1]),
        'certificate_flag' => $faker->randomElement([0, 1]),
        'coverage_content_flag' => $faker->randomElement([0, 1]),
        'document_path' => 'storage/documents/' . $faker->word() . '.svg',
        'created_pg' => 'TLpOrdarFatory',
        'created_at' => now(),
        'updated_pg' => 'TLpOrdarFatory',
        'updated_at' => now(),
    ];
});
