<?php

namespace App\Facades\OpenAI\Test;

use App\Facades\OpenAI\Contracts;

class ChatCompletion implements Contracts\ChatCompletionContract
{
    public function __construct(
        private ?string $content,
        private int $completion_tokens,
        private int $prompt_tokens,
        private int $total_tokens,
    ) {
        //
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getCompletionTokens(): int
    {
        return $this->completion_tokens;
    }

    public function getPromptTokens(): int
    {
        return $this->prompt_tokens;
    }

    public function getTotalTokens(): int
    {
        return $this->total_tokens;
    }
}
