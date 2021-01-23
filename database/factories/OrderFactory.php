<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Order;
use Illuminate\Support\Arr;
use Faker\Generator as Faker;

$factory->define(Order::class, function (Faker $faker) {
    return [
        'product_id'     => $faker->numberBetween(1,10),
        'number'         =>$faker->numberBetween(100,2000),
        'customer_name'  => $faker->name(),
        'customer_email' => $faker->freeEmail,
        'customer_mobile'=> $faker->phoneNumber,
        'status'         => Arr::random(['CREATED','PAYED','REJECTED']),
    ];
});
