<?php

namespace App\Containers\AppSection\Event\Notifications;

use App\Containers\AppSection\Event\Models\Event;
use App\Ship\Parents\Notifications\Notification as ParentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

final class EventInvitationNotification extends ParentNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Event $event,
        public readonly string $inviterName,
    ) {
    }

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage())
            ->subject('Запрошення: ' . $this->event->title)
            ->greeting('Вітаємо!')
            ->line($this->inviterName . ' запрошує вас на подію «' . $this->event->title . '».')
            ->line('Коли: ' . EventMailFormatter::period($this->event));

        if (null !== $this->event->location) {
            $message->line('Де: ' . $this->event->location);
        }

        return $message
            ->action('Відкрити календар', route('calendar.index'))
            ->line('Прийняти або відхилити запрошення можна в календарі.');
    }

    /**
     * @return array{type: string, title: string, message: string, url: string}
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'event_invitation',
            'title' => 'Запрошення: ' . $this->event->title,
            'message' => $this->inviterName . ' запрошує вас на подію. Коли: ' . EventMailFormatter::period($this->event),
            'url' => route('calendar.index'),
        ];
    }
}
