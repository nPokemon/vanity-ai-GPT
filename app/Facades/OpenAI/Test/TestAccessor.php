<?php

namespace App\Facades\OpenAI\Test;

use App\Facades\OpenAI\Contracts;
use App\Facades\OpenAI\Enums;
use Illuminate\Support\Facades\Process;

class TestAccessor implements Contracts\OpenAIContract
{
    public const MODEL = Enums\Model::GPT_35_TURBO_16K;

    public function createChatCompletion(Enums\Model $model, Contracts\MessagesCollectionContract $messages): Contracts\ChatCompletionContract
    {
        $completion = fake()->sentence(20);

        $completion_tokens = $this->countTokens(self::MODEL, $completion);

        $messages_array = $messages->getMessages();

        $messages_string = implode('', array_map(fn($message) => $message->getContent(), $messages_array));

        $prompt_tokens = $this->countTokens(self::MODEL, $messages_string);

        return new ChatCompletion(
            $completion,
            $completion_tokens,
            $prompt_tokens,
            $prompt_tokens + $completion_tokens
        );
    }

    public function countTokens(Enums\Model $mode, string $content): int
    {
        $result = Process::run(
            'python3 '
            .base_path()
            .'/storage/scripts/count_tokens.py '
            .'cl100k_base '
            .'"'.$content.'"'
        );

        return intval($result->output());
    }
}
