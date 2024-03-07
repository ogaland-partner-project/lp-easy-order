<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TBasicKnowledgeDetail;
use App\Models\TBasicKnowledge;
use Faker\Generator as Faker;

$factory->define(TBasicKnowledgeDetail::class, function (Faker $faker) {
    return [
        'basic_knowledge_id' => factory(TBasicKnowledge::class),
        'title' => $faker->word(),
        'detail' => $faker->sentence(),
        'col' => rand(1, 10),
        'sort_order' => rand(1, 10),
        'created_pg' => 'TBasicKnowledgeDetailFatory',
        'created_at' => now(),
        'updated_pg' => 'TBasicKnowledgeDetailFatory',
        'updated_at' => now(),
    ];
});
