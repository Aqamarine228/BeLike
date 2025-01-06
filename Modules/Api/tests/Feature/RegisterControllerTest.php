<?php

namespace Modules\Api\Tests\Feature;

use Illuminate\Support\Facades\Notification;
use Modules\Api\Models\User;
use Modules\Api\Notifications\VerifyEmail;
use Modules\Api\Tests\ApiTestCase;

class RegisterControllerTest extends ApiTestCase
{

    public function testRegisterSuccess(): void
    {
        Notification::fake();

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail(),
            'password' => 'nalkfgansd!@#fkjsdfF1337!',
            'password_confirmation' => 'nalkfgansd!@#fkjsdfF1337!'
        ];

        $this->postJson('/api/v1/register', $data)->assertOk();
        unset($data['password']);
        unset($data['password_confirmation']);
        $this->assertDatabaseHas('users', $data);
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => User::class,
            'name' => $data['email'],
        ]);

        Notification::assertSentTo(User::where($data)->first(), VerifyEmail::class);
    }

    public function testRegisterRequired(): void
    {
        $this->postJson('/api/v1/register')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function testRegisterValidation(): void
    {
        $data = [
            'name' => '123',
            'email' => 123,
            'password' => 'notStrongPassword',
            'password_confirmation' => 'notStrongPassword',
        ];

        $this->postJson('/api/v1/register', $data)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }
}
