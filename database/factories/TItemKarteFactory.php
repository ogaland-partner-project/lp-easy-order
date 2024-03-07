<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TItemKarte;
use App\Models\TLpOrder;
use Faker\Generator as Faker;

$factory->define(TItemKarte::class, function (Faker $faker) {
    return [
        'lp_order_id' => factory(TLpOrder::class),
        'goods_name' => $faker->word(),
        'trademark_flag' => $faker->randomElement([0, 1]),
        'chinese_characters' => $faker->word(),
        'kana' => $faker->word(),
        'romaji' => $faker->word(),
        'goods_specifications' => $faker->sentence(),
        'price_including_tax' => rand(1000, 10000),
        'jas_mark' => '',
        'jas_mark_folder' => 'storage/jas/' . $faker->word() . '.svg',
        'jas_mark_certification' => $faker->sentence(),
        'target_jendar' => $faker->randomElement(['男性', '女性']),
        'target_age' => $faker->randomElement([10, 20, 30, 40, 50, 60, 70]),
        'target_statue' => $faker->sentence(),
        'BM_goods_name1' => $faker->word(),
        'BM_url1' => $faker->url(),
        'BM_goods_name2' => $faker->word(),
        'BM_url2' => $faker->url(),
        'difference_point' => $faker->sentence(),
        'concept' => $faker->sentence(),
        'supplier_information_sharing' => $faker->sentence(),
        'strong_point' => $faker->word(),
        'created_pg' => 'TItemKarteFatory',
        'created_at' => now(),
        'updated_pg' => 'TItemKarteFatory',
        'updated_at' => now(),
    ];
});
