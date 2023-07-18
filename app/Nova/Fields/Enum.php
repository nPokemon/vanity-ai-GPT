<?php

namespace App\Nova\Fields;

use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

class Enum extends Select
{
    public function __construct($name, $attribute, private string $enum, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $options = (collect($enum::cases()))->map(function ($item) {
            return [
                'value' => $item->value,
                'name' => __(str_replace('_', ' ', ucfirst(strtolower($item->name)))),
            ];
        })->keyBy('value')->pluck('name')->toArray();

        return $this->options($options)->displayUsingLabels();
    }

    public function displayUsingLabels()
    {
        return $this->displayUsing(function ($value) {
            if ($value === null) {
                return null;
            }

            return __(
                str_replace(
                    '_',
                    ' ',
                    ucfirst(
                        strtolower(
                            ($this->enum::from($value))->name
                        )
                    )
                )
            );
        });
    }

    public function resolveDependentValue(NovaRequest $request)
    {
        return $this->value->value ?? $this->resolveDefaultValue($request);
    }
}
