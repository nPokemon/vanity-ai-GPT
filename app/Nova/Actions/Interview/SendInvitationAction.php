<?php

namespace App\Nova\Actions\Interview;

use App\Models;
use App\Services;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields;
use Laravel\Nova\Http\Requests\NovaRequest;

class SendInvitationAction extends Action
{
    use InteractsWithQueue, Queueable;

    public $name = 'Send invitation';

    public $confirmText = 'Send invitation to interviewee';

    public $confirmButtonText = 'Send';

    public function __construct(
        private Models\Interview $interview
    ) {
        //
    }

    public function handle(Fields\ActionFields $fields, Collection $models)
    {
        $notification_service = Services\Notification\NotificationService::make();
        $interview_service = Services\Interview\InterviewService::make();

        $interview = $models[0];

        $notification_service->sendInterviewInvitation(
            $interview,
            $fields->subject,
            $fields->greeting,
            $fields->text,
            $interview_service->makeInviteUrl($interview)
        );

        $interview_service->setInterviewInvitationHasSent($interview);
    }

    public function fields(NovaRequest $request)
    {
        return [
            Fields\Text::make(__('Subject'), 'subject')
                ->default(config('app.name').' '.__('interview invitation'))
                ->rules('required', 'string', 'max:255'),

            Fields\Text::make(__('Greeting'), 'greeting')
                ->default(__('Dear :user_name', [
                    'user_name' => $this->interview?->interviewee?->name,
                ]))
                ->rules('required', 'string', 'max:255'),

            Fields\Textarea::make(__('Text'), 'text')
                ->default(__("We invite you to participate in an interview with our AI system. Your insights and expertise are valuable to us as we continue to develop and enhance our artificial intelligence technology.\nTo start the interview, please click the button below:"))
                ->rules('required', 'string', 'max:255')
                ->rows(10),
        ];
    }
}
