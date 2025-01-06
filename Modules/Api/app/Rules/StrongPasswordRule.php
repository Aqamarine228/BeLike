<?php

namespace Modules\Api\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class StrongPasswordRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $validator = Validator::make(['password' => $value], [
            'password' => [Password::min(12)->mixedCase()->numbers()->symbols()->uncompromised()]
        ]);

        if ($validator->fails()) {
            $fail($validator->errors()->first('password'));
        }
    }
}
