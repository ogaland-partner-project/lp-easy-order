<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TLpOrder;
use Faker\Generator as Faker;

$factory->define(TLpOrder::class, function (Faker $faker) {
    return [
        'product_name' => $faker->word(),
        'product_code' => $faker->bothify('??####'),
        'description' => $faker->sentence(),
        'status' => $faker->randomElement([0, 1, 2, 3]),
        'requirement_flag' => $faker->randomElement([0, 1]),
        'created_pg' => 'TLpOrderFatory',
        'created_at' => now(),
        'updated_pg' => 'TLpOrderFatory',
        'updated_at' => now(),
    ];
});
