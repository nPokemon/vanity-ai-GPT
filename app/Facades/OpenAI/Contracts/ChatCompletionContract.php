<?php

namespace App\Facades\OpenAI\Contracts;

interface ChatCompletionContract
{
    public function getContent(): ?string;

    public function getCompletionTokens(): int;

    public function getPromptTokens(): int;

    public function getTotalTokens(): int;
}
