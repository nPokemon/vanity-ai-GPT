<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class OpenAIFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'OpenAIAccessor';
    }
}
