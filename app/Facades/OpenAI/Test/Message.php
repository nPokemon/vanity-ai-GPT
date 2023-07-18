<?php

namespace App\Facades\OpenAI\Test;

use App\Facades\OpenAI\Contracts;
use App\Facades\OpenAI\Enums;

class Message implements Contracts\MessageContract
{
    private Enums\MessageRole $role;

    private string $content;

    public function setRole(Enums\MessageRole $role): void
    {
        $this->role = $role;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getRole(): Enums\MessageRole
    {
        return $this->role;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
