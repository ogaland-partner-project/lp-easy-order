<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TBasicKnowledgeImage;
use App\Models\TBasicKnowledge;
use Faker\Generator as Faker;

$factory->define(TBasicKnowledgeImage::class, function (Faker $faker) {
    $basicKnowledge = factory(TBasicKnowledge::class)->create();
    return [
        'basic_knowledge_id' => $basicKnowledge->id,
        'image_path' => '/storage/lp_order/' . $basicKnowledge->lpOrder->id . '/BasicKnowledge/' . $faker->word() . '.jpg',
        'image_memo' => $faker->sentence(),
        'created_pg' => 'TBasicKnowledgeDetailFatory',
        'created_at' => now(),
        'updated_pg' => 'TBasicKnowledgeDetailFatory',
        'updated_at' => now(),
    ];
});
