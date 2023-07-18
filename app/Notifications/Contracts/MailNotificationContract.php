<?php

namespace App\Notifications\Contracts;

use App\Notifications\Messages\MailMessage;

interface MailNotificationContract
{
    public function toMail(mixed $notifiable): MailMessage;
}
