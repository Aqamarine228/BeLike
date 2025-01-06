<?php

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Api\Http\Requests\ApiEmailVerificationRequest;

class EmailVerificationController extends ApiController
{

    public function resend(Request $request): JsonResponse
    {
        $request->user()->sendEmailVerificationNotification();

        return $this->respondSuccess("email verification link sent successfully");
    }
    public function verify(ApiEmailVerificationRequest $request)
    {
        $request->fulfill();

        return view('email-verified');
    }

    public function checkVerification(Request $request): JsonResponse
    {
        return $this->respondSuccess([
            'verified' => $request->user()->hasVerifiedEmail()
        ]);
    }
}
