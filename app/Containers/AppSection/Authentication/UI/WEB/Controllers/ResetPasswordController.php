<?php

namespace App\Containers\AppSection\Authentication\UI\WEB\Controllers;

use App\Containers\AppSection\Authentication\Actions\PasswordReset\ResetPasswordAction;
use App\Containers\AppSection\Authentication\UI\WEB\Requests\ResetPasswordRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

final class ResetPasswordController extends WebController
{
    public function __invoke(ResetPasswordRequest $request, ResetPasswordAction $action): RedirectResponse
    {
        try {
            $action->run($request->sanitize([
                'token',
                'email',
                'password',
                'password_confirmation',
            ]));
        } catch (ValidationException) {
            // Invalid/expired token and unknown email look the same to the user.
            throw ValidationException::withMessages(['token' => 'Посилання для скидання пароля недійсне або прострочене. Запросіть нове.']);
        }

        Inertia::flash('status', 'Пароль змінено. Увійдіть з новим паролем.');

        return redirect()->action([LoginController::class, 'showForm']);
    }
}
