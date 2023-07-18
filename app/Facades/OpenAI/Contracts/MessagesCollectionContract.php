<?php

namespace App\Facades\OpenAI\Contracts;

interface MessagesCollectionContract
{
    public function setMessages(MessageContract ...$messages): void;

    public function getMessages(): array;
}
