<?php

namespace App\Containers\AppSection\Invitation\Notifications;

use App\Ship\Parents\Notifications\Notification as ParentNotification;
use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

final class InvitationNotification extends ParentNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $token,
        public readonly string $inviterName,
        public readonly CarbonInterface $expiresAt,
    ) {
    }

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appName = config()->string('app.name');

        return (new MailMessage())
            ->subject('Запрошення до ' . $appName)
            ->greeting('Вітаємо!')
            ->line($this->inviterName . ' запрошує вас до робочого простору ' . $appName . '.')
            ->action('Зареєструватися', route('register.form', ['token' => $this->token]))
            ->line('Посилання одноразове і діє до ' . $this->expiresAt->format('d.m.Y H:i') . '.')
            ->line('Якщо ви не очікували цього листа, просто проігноруйте його.');
    }
}
