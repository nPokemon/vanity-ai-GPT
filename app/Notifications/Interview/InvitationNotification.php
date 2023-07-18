<?php

namespace App\Notifications\Interview;

use App\Enums;
use App\Notifications\AbstractNotification;
use App\Notifications\Channels;
use App\Notifications\Contracts;
use App\Notifications\Messages;

class InvitationNotification extends AbstractNotification implements Contracts\MailNotificationContract
{
    public function __construct(
        private string $subject,
        private string $greeting,
        private string $text,
        private string $url
    ) {
        //
    }

    public function via(mixed $notifiable): array
    {
        return [
            Channels\MailChannel::class,
        ];
    }

    public function viaQueues(): array
    {
        return [
            Channels\MailChannel::class => Enums\Queue::NOTIFICATIONS->value,
        ];
    }

    public function toMail(mixed $notifiable): Messages\MailMessage
    {
        return Messages\MailMessage::make()
            ->subject($this->subject)
            ->greeting($this->greeting)
            ->line($this->text)
            ->action(__('Start interview'), $this->url);
    }

    public function toArray(mixed $notifiable): array
    {
        return [
            'subject' => $this->subject,
            'greeting' => $this->greeting,
            'lines' => $this->text,
            'action' => $this->url,
        ];
    }
}
