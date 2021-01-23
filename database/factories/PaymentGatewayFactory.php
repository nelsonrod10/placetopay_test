<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\PaymentGateway;
use Faker\Generator as Faker;

$factory->define(PaymentGateway::class, function (Faker $faker) {
    return [
        'order_id'      => $faker->numberBetween(100,3000),
        'enterprise'    => $faker->company,
        'payment_data'  => $faker->text(150)
    ];    
});
