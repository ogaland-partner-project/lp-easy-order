<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TBasicKnowledgeUrl;
use App\Models\TBasicKnowledge;
use Faker\Generator as Faker;

$factory->define(TBasicKnowledgeUrl::class, function (Faker $faker) {
    $basicKnowledge = factory(TBasicKnowledge::class)->create();
    return [
        'basic_knowledge_id' => $basicKnowledge->id,
        'url' => 'https://example.jp/' . $basicKnowledge->id . '/' . $faker->word(),
        'created_pg' => 'TBasicKnowledgeDetailFatory',
        'created_at' => now(),
        'updated_pg' => 'TBasicKnowledgeDetailFatory',
        'updated_at' => now(),
    ];
});
