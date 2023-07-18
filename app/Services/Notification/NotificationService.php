<?php

namespace App\Services\Notification;

use App\Models;
use App\Notifications;
use App\Services\AbstractService;
use Illuminate\Support\Facades\Notification;

class NotificationService extends AbstractService
{
    public function sendInterviewInvitation(Models\Interview $interview, string $subject, string $greeting, string $text, string $url): bool
    {
        $notification = new Notifications\Interview\InvitationNotification(
            $subject,
            $greeting,
            $text,
            $url
        );

        Notification::route('mail', $interview->interviewee->email)
            ->notify($notification);

        return true;
    }
}
