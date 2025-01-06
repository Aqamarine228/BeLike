<?php

namespace Modules\Api\app\Http\Controllers;

use App\Enums\SocialiteProvider;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;
use Laravel\Socialite\Facades\Socialite;
use Modules\Api\Models\User;
use Modules\Api\Http\Controllers\ApiController;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends ApiController
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !password_verify($validated['password'], $user->password)) {
            return $this->respondError(
                "provided credentials are incorrect.",
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return $this->respondSuccess([
            'token' => $user->createToken($user->email)->plainTextToken,
            'email_verified' => (bool)$user->email_verified_at,
        ]);
    }

    public function socialLogin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'provider' => ['required', new Enum(SocialiteProvider::class)],
            'access_token' => 'required|string',
        ]);

        try {
            $providerUser = Socialite::driver($validated['provider'])
                ->stateless()
                ->userFromToken($validated['access_token']);
        } catch (ClientException $exception) {
            return $this->respondError("wrong credentials");
        }

        if (!$providerUser->getEmail()) {
            return $this->respondError('wrong token');
        }

        $user = DB::transaction(function () use ($providerUser, $validated) {
            $user = User::firstOrCreate(
                [
                    'email' => $providerUser->getEmail()
                ],
                [
                    'name' => $providerUser->getName(),
                    'password' => Hash::make(Str::random(32)),
                    'email_verified_at' => now(),
                ]
            );

            $user->socialiteProviders()->createOrFirst([
                'provider' => $validated['provider'],
                'provider_id' => $providerUser->getId(),
            ]);

            return $user;
        });

        return $this->respondSuccess([
            'token' => $user->createToken($user->email)->plainTextToken,
            'has_projects' => $user->has_projects,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->respondSuccess("log out successful");
    }
}
