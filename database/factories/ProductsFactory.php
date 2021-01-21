<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'name' => $faker->sentence(), 
        'price' => $faker->numberBetween(10000, 250000),
        'currency' => 'COP',
        'description' => $faker->paragraph(),
    ];
});
