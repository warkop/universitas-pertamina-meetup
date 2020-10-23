<?php

namespace Database\Factories;

use App\Models\ResearchGroup;
use App\Models\ResearchGroupDiscussion;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResearchGroupDiscussionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ResearchGroupDiscussion::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $researchGroup = ResearchGroup::inRandomOrder()->first();
        return [
            'research_group_id' => $researchGroup->id,
            'name' => $this->faker->name(),
            'desc' => $this->faker->realText(),
        ];
    }
}
