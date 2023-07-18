<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

abstract class AbstractNotification extends Notification implements ShouldQueue
{
    use Queueable;

    abstract public function via(object $notifiable): array;

    abstract public function viaQueues(): array;

    abstract public function toArray(object $notifiable): array;
}
