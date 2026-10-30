<?php

namespace App\Containers\AppSection\Authentication\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

final class ResetPassword extends BaseResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $broker = config()->string('auth.defaults.passwords');

        return (new MailMessage())
            ->subject('Скидання пароля в ' . config()->string('app.name'))
            ->greeting('Вітаємо!')
            ->line('Ви отримали цей лист, бо для вашого акаунта надійшов запит на скидання пароля.')
            ->action('Скинути пароль', $this->resetUrl($notifiable))
            ->line('Посилання діє ' . config()->integer("auth.passwords.{$broker}.expire") . ' хв.')
            ->line('Якщо ви не запитували скидання пароля, просто проігноруйте цей лист.');
    }
}
