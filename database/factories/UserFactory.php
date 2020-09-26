<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'email'             => $this->faker->unique()->safeEmail,
            'type'              => $this->faker->numberBetween(0, 1),
            'owner_id'          => $this->faker->numberBetween(2, 3),
            'role_id'           => $this->faker->numberBetween(2, 3),
            'password'          => bcrypt('qwerty'), // password
            'remember_token'    => Str::random(10),
            'email_verified_at' => now(),
            'confirm_at'        => now(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ];
    }
}
