<?php

namespace App\Containers\AppSection\Event\Notifications;

use App\Containers\AppSection\Event\Models\Event;
use App\Ship\Parents\Notifications\Notification as ParentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

final class EventReminderNotification extends ParentNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Event $event,
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
        $message = (new MailMessage())
            ->subject('Нагадування: ' . $this->event->title)
            ->greeting('Нагадуємо про подію')
            ->line('«' . $this->event->title . '»')
            ->line('Коли: ' . EventMailFormatter::period($this->event));

        if (null !== $this->event->location) {
            $message->line('Де: ' . $this->event->location);
        }

        return $message->action('Відкрити календар', route('calendar.index'));
    }
}
