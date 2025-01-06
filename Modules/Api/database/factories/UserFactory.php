<?php

namespace Modules\Api\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Api\Models\User;

class UserFactory extends Factory
{

    protected $model = User::class;
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('secret'),
        ];
    }
}
