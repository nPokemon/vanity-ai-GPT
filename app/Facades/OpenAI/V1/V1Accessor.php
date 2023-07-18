<?php

namespace App\Facades\OpenAI\V1;

use App\Facades\OpenAI\Contracts;
use App\Facades\OpenAI\Enums;
use Illuminate\Support\Facades\Process;
use OpenAI;
use OpenAI\Client;

class V1Accessor implements Contracts\OpenAIContract
{
    private Client $client;

    public function __construct()
    {
        $this->client = OpenAI::client(config('services.openai.api_key'));
    }

    public function createChatCompletion(Enums\Model $model, Contracts\MessagesCollectionContract $messages): Contracts\ChatCompletionContract
    {
        $messages_array = [];
        foreach ($messages->getMessages() as $message) {
            $messages_array[] = [
                'role' => $message->getRole()->value,
                'content' => $message->getContent(),
            ];
        }

        $response = $this->client->chat()->create([
            'model' => $model->value,
            'messages' => $messages_array,
        ]);

        $usage = $response->usage;

        return new ChatCompletion(
            $response->choices[0]->message->content,
            $usage->completionTokens,
            $usage->promptTokens,
            $usage->totalTokens
        );
    }

    public function countTokens(Enums\Model $model, string $content): int
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
