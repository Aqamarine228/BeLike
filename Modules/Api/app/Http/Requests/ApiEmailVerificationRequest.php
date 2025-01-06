<?php

namespace Modules\Api\Http\Requests;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;

class ApiEmailVerificationRequest extends EmailVerificationRequest
{
    public function authorize(): bool
    {
        if (!$this->hasValidSignature()) {
            return false;
        }
        Auth::loginUsingId($this->route('id'));
        return parent::authorize();
    }
}
