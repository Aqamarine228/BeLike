<?php

namespace Modules\Api\Tests\Feature;

use Illuminate\Support\Facades\Hash;
use Modules\Api\Models\User;
use Modules\Api\Tests\ApiTestCase;

class LoginControllerTest extends ApiTestCase
{

    public function testLoginSuccess(): void
    {
        $password = 'asdfm123!Q$#@123';

        $user = User::factory()->state([
            'password' => Hash::make($password),
        ])->create();

        $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => $password,
        ])->assertOk();

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'name' => $user->email,
        ]);
    }

    public function testLoginWrongCredentials(): void
    {
        $this->postJson('/api/v1/login', [
            'email' => 'asdf@test.mail',
            'password' => 'asdfm123!Q$#@123',
        ])->assertUnprocessable();
    }

    public function testLoginValidated(): void
    {
        $this->postJson('/api/v1/login', [
            'email' => 'asdf',
            'password' => 'asdf',
        ])->assertUnprocessable()->assertJsonValidationErrors(['email']);
    }

    public function testLoginRequired(): void
    {
        $this->postJson('/api/v1/login')->assertUnprocessable()->assertJsonValidationErrors(['email', 'password']);
    }

    public function testLogout(): void
    {
        $this->app['auth']->guard('sanctum')->forgetUser();
        $token = $this->user->createToken('test')->plainTextToken;
        $this->postJson('/api/v1/logout', [], [
            'Authorization' => "Bearer $token"
        ])->assertOk();
        $this->assertDatabaseEmpty('personal_access_tokens');
    }
}
