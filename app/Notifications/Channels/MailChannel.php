<?php

namespace App\Notifications\Channels;

use App\Notifications\Contracts\MailNotificationContract;
use Illuminate\Notifications\Channels\MailChannel as BaseChannel;

class MailChannel
{
    public function __construct(
        private BaseChannel $base_channel
    ) {
        //
    }

    public function send(mixed $notifiable, MailNotificationContract $notification): void
    {
        $this->base_channel->send($notifiable, $notification);
    }
}
