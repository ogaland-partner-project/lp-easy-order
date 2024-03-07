<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TBasicKnowledge;
use App\Models\TLpOrder;
use Faker\Generator as Faker;

$factory->define(TBasicKnowledge::class, function (Faker $faker) {
    return [
        'lp_order_id' => factory(TLpOrder::class),
        'question' => $faker->sentence(),
        'others' => $faker->sentence(),
        'created_pg' => 'TBasicKnowledgeFatory',
        'created_at' => now(),
        'updated_pg' => 'TBasicKnowledgeFatory',
        'updated_at' => now(),
    ];
});
