<?php

namespace Database\Factories;

use App\Models\ResearchGroupComment;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResearchGroupCommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ResearchGroupComment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'research_group_discussion_id' => 1,
            'comment' => $this->faker->realText(),
        ];
    }
}
