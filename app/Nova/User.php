<?php

namespace App\Nova;

use App\Models;
use Laravel\Nova\Fields;
use Laravel\Nova\Http\Requests\NovaRequest;

class User extends Resource
{
    public static $model = Models\User::class;

    public static $title = 'name';

    public static $search = [
        'id', 'name', 'email',
    ];

    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->withCount('interviews');
    }

    public static function detailQuery(NovaRequest $request, $query)
    {
        return $query->withCount('interviews');
    }

    public static function label()
    {
        return __('Interviewees');
    }

    public static function singularLabel()
    {
        return __('Interviewee');
    }

    public function fields(NovaRequest $request)
    {
        return [
            Fields\ID::make()->sortable(),

            Fields\Text::make(__('Name'), 'name')
                ->rules('required', 'string', 'max:255')
                ->sortable(),

            Fields\Email::make(__('Email'), 'email')
                ->rules('required', 'string', 'max:255')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{resourceId}')
                ->sortable(),

            ...$this->getTimestampsFields(),

            Fields\HasMany::make(__('Interviews'), 'interviews', Interview::class),
        ];
    }
}
