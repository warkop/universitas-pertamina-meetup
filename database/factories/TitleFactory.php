<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Title;
use Faker\Generator as Faker;

$factory->define(Title::class, function (Faker $faker) {
    return [
        'name' => $faker->firstName(),
    ];
});
