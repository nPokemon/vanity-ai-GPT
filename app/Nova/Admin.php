<?php

namespace App\Nova;

use App\Models;
use Illuminate\Validation\Rules;
use Laravel\Nova\Fields;
use Laravel\Nova\Http\Requests\NovaRequest;

class Admin extends Resource
{
    public static $model = Models\Admin::class;

    public static $title = 'name';

    public static $search = [
        'id', 'name', 'email',
    ];

    public function fields(NovaRequest $request)
    {
        return [
            Fields\ID::make()->sortable(),

            Fields\Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Fields\Text::make('Email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:admins,email')
                ->updateRules('unique:admins,email,{{resourceId}}'),

            Fields\Password::make('Password')
                ->onlyOnForms()
                ->creationRules('required', Rules\Password::defaults())
                ->updateRules('nullable', Rules\Password::defaults()),

            Fields\Timezone::make(__('Timezone'), 'timezone')
                ->rules('required')
                ->sortable()
                ->filterable()
                ->default('UTC'),

            ...$this->getTimestampsFields(),
        ];
    }
}
