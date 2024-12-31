<?php

namespace Database\Factories;

use App\Models\SantaGroup;

class SantaGroupFactory
{
    public function definition(): array{
        return [
            'members' => fake()->name,
            'budget' => fake()->name,
        ];
    }
}