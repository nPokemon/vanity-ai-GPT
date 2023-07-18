<?php

namespace App\Nova;

use App\Enums;
use App\Models;
use App\Nova\Fields as CustomFields;
use Laravel\Nova\Fields;
use Laravel\Nova\Http\Requests\ActionRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

class Interview extends Resource
{
    public static $model = Models\Interview::class;

    public static $title = 'title';

    public static $search = [
        'id', 'slug', 'title',
    ];

    public function fields(NovaRequest $request)
    {
        return [
            Fields\ID::make()->sortable(),

            Fields\BelongsTo::make(__('Interviewee'), 'interviewee', User::class)
                ->showCreateRelationButton()
                ->filterable(),

            Fields\Text::make(__('Title'), 'title')
                ->placeholder(__('Title for future interview article'))
                ->rules('required', 'string', 'max:255'),

            Fields\Slug::make(__('Slug'), 'slug')
                ->placeholder(__('URI for future interview article'))
                ->from('title')
                ->rules('required', 'string', 'alpha_dash', 'max:255')
                ->creationRules('unique:interviews,slug')
                ->updateRules('unique:interviews,slug,{{resourceId}}'),

            Fields\Textarea::make(__('Description'), 'description')
                ->placeholder(__('Some text description for future interview article'))
                ->rules('nullable', 'string', 'max:5000'),

            CustomFields\Enum::make(__('Status'), 'status', Enums\Interview\Status::class)
                ->exceptOnForms()
                ->sortable()
                ->filterable(),

            new Panel(__('AI settings'), $this->getAISettingsFields()),

            Fields\DateTime::make(__('Invitation sent date'), 'invitation_sent_at')
                ->exceptOnForms()
                ->sortable()
                ->filterable(),

            Fields\DateTime::make(__('Start date'), 'started_at')
                ->exceptOnForms()
                ->sortable()
                ->filterable(),

            Fields\DateTime::make(__('Finish date'), 'finished_at')
                ->exceptOnForms()
                ->sortable()
                ->filterable(),

            ...$this->getTimestampsFields(),
        ];
    }

    private function getAISettingsFields(): array
    {
        return [
            Fields\Textarea::make(__('Bot personality'), 'ai_personality')
                ->placeholder(__('Instructions for the bot personality'))
                ->rows(20)
                ->rules('required', 'string', 'max:10000')
                ->default('You are a bot-journalist with name Vanity AI conducting an interview with [user name and description].'),

            Fields\Textarea::make(__('Bot instructions'), 'ai_instructions')
                ->placeholder(__('Instructions for the bot behavior model'))
                ->rows(30)
                ->rules('required', 'string', 'max:10000')
                ->default("Your task is to ask questions and maintain a continuous dialogue similar to a real interview. Remember that you are a professional journalist, and your goal is to learn more about the interviewee and their experiences. Here are some recommendations:\n- Introduce yourself and ask the first question: Begin the interview by introducing yourself and asking the user to tell a bit about themselves and their experiences.\n- Show interest: The user's answers may contain interesting information. Strive to learn more by asking follow-up questions and requesting examples.\n- Personalize your questions: Use the provided answers and information about the user to generate new questions. Demonstrate that you are attentively listening by mentioning details from previous responses.\n- Be professional: Adhere to journalistic etiquette. Be polite, respectful, and avoid bias. Try to create a comfortable atmosphere for the interview.\n- Maintain continuity: Aim to continue the dialogue by generating new questions based on the user's previous answers and the available information about them.\n- You are a professional bot-journalist, and your task is to conduct an interview with [user name]. Good luck!"),

            Fields\Textarea::make(__('Start message'), 'start_message')
                ->placeholder(__('Content of the first message from user'))
                ->rows(15)
                ->rules('required', 'string', 'max:10000')
                ->default('Introduce yourself and ask first question'),

            Fields\Textarea::make(__('End message'), 'end_message')
                ->placeholder(__('Content of the last message from user'))
                ->rows(15)
                ->rules('required', 'string', 'max:10000')
                ->default('User wants to end interview. Generate a farewell message thanking the user for participating'),
        ];
    }

    public function actions(NovaRequest $request): array
    {
        return [
            Actions\Interview\SendInvitationAction::make($this->resource)->onlyOnDetail()->canSee(function ($request) {
                if ($request instanceof ActionRequest) {
                    return true;
                }

                return $this->isStarted(false);
            }),
        ];
    }
}
