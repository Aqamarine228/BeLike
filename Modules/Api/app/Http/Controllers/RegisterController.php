<?php

namespace Modules\Api\app\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Api\Http\Controllers\ApiController;
use Modules\Api\Models\User;
use Modules\Api\Rules\StrongPasswordRule;

class RegisterController extends ApiController
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email:filter|max:255|unique:users,email',
            'name' => 'required|string|max:255|min:5',
            'password'=> ['required', new StrongPasswordRule, 'confirmed'],
        ]);

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'email' => $validated['email'],
                'name' => $validated['name'],
                'password' => Hash::make($validated['password']),
            ]);

            $user->sendEmailVerificationNotification();

            return $user;
        });

        return $this->respondSuccess($user->createToken($user['email'])->plainTextToken);
    }
}
