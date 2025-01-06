<?php

namespace Modules\Api\Tests\Feature;

use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\Fluent\AssertableJson;
use Modules\Api\Models\User;
use Modules\Api\Notifications\VerifyEmail;
use Modules\Api\Tests\ApiTestCase;

class EmailVerificationController extends ApiTestCase
{

    public function testVerifySigned(): void
    {
        $this->postJson('/api/v1/email/verify/1/1')->assertForbidden();
    }

    public function testResendSuccess(): void
    {
        Notification::fake();
        $this->post('/api/v1/email/resend')->assertOk();
        Notification::assertSentTo(auth()->user(), VerifyEmail::class);
    }

    public function testResendThrottle(): void
    {
        $this->post('/api/v1/email/resend');
        $this->post('/api/v1/email/resend')->assertTooManyRequests();
    }

    public function testVerificationSuccess(): void
    {
        $this
            ->post('/api/v1/email/check-verification')
            ->assertOk()
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('success', true)
                ->where('response', [
                    'verified' => true,
                ]));
    }

    public function testVerificationSuccessNotVerified(): void
    {
        $newUser = User::factory()->state([
            'email_verified_at' => null,
        ])->create();

        $this
            ->actingAs($newUser)
            ->post('/api/v1/email/check-verification')
            ->assertOk()
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('success', true)
                ->where('response', [
                    'verified' => false,
                ]));
    }
}
