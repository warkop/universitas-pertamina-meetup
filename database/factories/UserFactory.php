<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'username'          => $faker->unique()->userName,
        'type'              => $faker->numberBetween(0, 1),
        'owner_id'          => $faker->numberBetween(2, 3),
        'role_id'           => $faker->numberBetween(2, 3),
        'status'            => 1,
        'password'          => bcrypt('qwerty'), // password
        'remember_token'    => Str::random(10),
        'created_at'        => now(),
        'updated_at'        => now(),
    ];
});
