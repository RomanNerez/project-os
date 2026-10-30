<?php

namespace App\Containers\AppSection\Authentication\Actions\PasswordReset;

use App\Containers\AppSection\Authentication\UI\WEB\Controllers\ShowResetPasswordFormController;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Actions\Action as ParentAction;

final class GenerateUrlAction extends ParentAction
{
    public function __invoke(User $notifiable, string $token): string
    {
        return action(ShowResetPasswordFormController::class, [
            'token' => $token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);
    }
}
