<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ResearchGroupComment;
use Faker\Generator as Faker;

$factory->define(ResearchGroupComment::class, function (Faker $faker) {

    return [
        'research_group_discussion_id' => 1,
        'comment' => $faker->realText(),
    ];
});
