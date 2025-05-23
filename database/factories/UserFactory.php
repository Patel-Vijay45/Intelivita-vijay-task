<?php

namespace Database\Factories;
// database/factories/UserFactory.php
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'full_name' => $this->faker->name,
        ];
    }
}
