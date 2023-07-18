<?php

namespace App\Notifications\Messages;

use App\Traits\Makeable;
use Illuminate\Notifications\Messages\MailMessage as BaseMessage;

class MailMessage extends BaseMessage
{
    use Makeable;
}
