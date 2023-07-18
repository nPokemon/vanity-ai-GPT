<?php

namespace App\Facades\OpenAI\V1;

use App\Facades\OpenAI\Contracts;

class MessagesCollection implements Contracts\MessagesCollectionContract
{
    private array $messages;

    public function setMessages(Contracts\MessageContract ...$messages): void
    {
        $this->messages = $messages;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }
}
