<?php

namespace App\Facades\OpenAI\Enums;

enum Model: string
{
    case GPT_35_TURBO_16K = 'gpt-3.5-turbo-16k';
    case GPT_4_32K = 'gpt-4-32k';
}
