<?php

namespace App\Facades\OpenAI\Contracts;

use App\Facades\OpenAI\Enums;

interface OpenAIContract
{
    public function createChatCompletion(Enums\Model $model, MessagesCollectionContract $messages): ChatCompletionContract;

    public function countTokens(Enums\Model $model, string $content): int;
}
