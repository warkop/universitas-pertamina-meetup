<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ResearchGroup;
use App\Models\ResearchGroupDiscussion;
use Faker\Generator as Faker;

$factory->define(ResearchGroupDiscussion::class, function (Faker $faker) {
    $researchGroup = ResearchGroup::inRandomOrder()->first();
    return [
        'research_group_id' => $researchGroup->id,
        'name' => $faker->name(),
        'desc' => $faker->realText(),
    ];
});
