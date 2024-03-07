<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\TFinalDesignConfirmation;
use Faker\Generator as Faker;

$factory->define(TFinalDesignConfirmation::class, function (Faker $faker) {

    $id=1;
    $lp_order_id=2;
    $image_path="/storage/lp_order/1/finaldesignconfirmation/2_origenal.jpg";
    $design_memo="<P>ここにHTML記法で修正必要内容が入る</P>";
    $law_support_memo="<P>ここにHTML記法で修正必要内容が入る</P>";
    $info_manage_memo="<P>ここにHTML記法で再修正・メモが入る</P>";
    $sort_order=1;

    return [
        'id' => $id,
        'lp_order_id' => $lp_order_id,
        'image_path' => $image_path,
        'design_memo' => $design_memo,
        'law_support_memo' => $law_support_memo,
        'info_manage_memo' => $info_manage_memo,
        'sort_order' => $sort_order
    ];
});
