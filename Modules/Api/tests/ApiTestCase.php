<?php

namespace Modules\Api\Tests;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Api\Models\User;
use Tests\TestCase;

class ApiTestCase extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();

        $this->user = User::factory()->create();


        $this->actingAs($this->user, 'sanctum');
        $this->withHeader('accept', 'application/json');
    }

}
