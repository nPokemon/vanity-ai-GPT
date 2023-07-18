<?php

namespace App\Traits;

trait Makeable
{
    public static function make()
    {
        return app()->make(get_called_class());
    }
}
