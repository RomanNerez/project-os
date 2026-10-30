<?php

namespace App\Containers\AppSection\Authentication\UI\WEB\Controllers;

use App\Containers\AppSection\Authentication\Actions\PasswordReset\ForgotPasswordAction;
use App\Containers\AppSection\Authentication\UI\WEB\Requests\ForgotPasswordRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

final class ForgotPasswordController extends WebController
{
    public function __invoke(ForgotPasswordRequest $request, ForgotPasswordAction $action): RedirectResponse
    {
        try {
            $action->run($request->string('email')->toString());
        } catch (ValidationException $exception) {
            // Don't reveal whether an account exists: only the throttle error reaches the user.
            if (!array_key_exists('throttle', $exception->errors())) {
                return $this->sent();
            }

            throw ValidationException::withMessages(['email' => 'Посилання вже надіслано. Спробуйте пізніше.']);
        }

        return $this->sent();
    }

    private function sent(): RedirectResponse
    {
        Inertia::flash('status', 'Якщо акаунт з таким email існує, ми надіслали на нього посилання для скидання пароля.');

        return back();
    }
}
