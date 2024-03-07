<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ItemKarte;
use Faker\Generator as Faker;

$factory->define(ItemKarte::class, function (Faker $faker) {
    return [
        'id' => $faker->id,
        'item_karte_id' => $faker->item_karte_id,
    ];
});
