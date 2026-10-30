<?php

namespace App\Containers\AppSection\Authentication\Actions\PasswordReset;

use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

final class ForgotPasswordAction extends ParentAction
{
    public function run(string $email): string
    {
        $status = Password::sendResetLink(['email' => $email]);

        return match ($status) {
            Password::RESET_LINK_SENT => __($status),
            Password::RESET_THROTTLED => throw ValidationException::withMessages(['throttle' => __($status)]),
            default => throw ValidationException::withMessages(['email' => __($status)]),
        };
    }
}
