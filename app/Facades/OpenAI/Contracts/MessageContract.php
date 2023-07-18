<?php

namespace App\Facades\OpenAI\Contracts;

use App\Facades\OpenAI\Enums;

interface MessageContract
{
    public function setRole(Enums\MessageRole $role): void;

    public function setContent(string $content): void;

    public function getRole(): Enums\MessageRole;

    public function getContent(): string;
}
