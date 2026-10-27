<?php

use App\Containers\AppSection\Invitation\UI\WEB\Controllers\ResendInvitationController;
use Illuminate\Support\Facades\Route;

Route::post('users/invitations/{id}/resend', ResendInvitationController::class)
    ->whereNumber('id')
    ->name('users.invitations.resend')
    ->middleware(['auth:web']);
