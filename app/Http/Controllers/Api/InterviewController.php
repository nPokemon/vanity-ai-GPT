<?php

namespace App\Http\Controllers\Api;

use App\Facades\OpenAI;
use App\Facades\OpenAIFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\Message as MessageRequests;
use App\Http\Resources\Interview as InterviewResources;
use App\Http\Resources\Message as MessageResources;
use App\Models;
use App\Services;
use Illuminate\Http\Request;
use Pagination;
use Response;

class InterviewController extends Controller
{
    public function __construct(
        private Services\Interview\InterviewService $interview_service
    ) {
        //
    }

    public function getInterview(Request $request, Models\Interview $interview)
    {
        return Response::send(new InterviewResources\DefaultResource($interview));
    }

    public function startInterview(Request $request, Models\Interview $interview)
    {
        $message = $this->interview_service->startInterview($interview);

        return Response::send(new MessageResources\DefaultResource($message));
    }

    public function endInterview(Request $request, Models\Interview $interview)
    {
        $message = $this->interview_service->endInterview($interview);

        return Response::send(new MessageResources\DefaultResource($message));
    }

    public function submitInterview(Request $request, Models\Interview $interview)
    {
        $result = $this->interview_service->submitInterview($interview);

        return Response::send($result);
    }

    public function sendMessage(MessageRequests\CreateRequest $request, Models\Interview $interview)
    {
        $content = $request->input('content');

        $messages = $this->interview_service->sendMessage($interview, $content);

        return Response::send(['messages_set' =>
            [
                'user_message' => new MessageResources\DefaultResource($messages[0]),
                'chat_completion' => new MessageResources\DefaultResource($messages[1])
            ]
        ]);
    }

    public function getMessages(Request $request, Models\Interview $interview)
    {
        $messages = $this->interview_service->getMessages($interview);

        return Response::send(
            new Pagination($messages, MessageResources\DefaultResource::class)
        );
    }

    public function deleteMessage(Request $request, Models\Interview $interview, Models\Message $message)
    {
        $result = $this->interview_service->deleteMessage($interview, $message);

        return Response::send($result);
    }
    public function skipMessage(Request $request, Models\Interview $interview, Models\Message $message)
    {
        $result = $this->interview_service->skipMessage($interview, $message);

        return Response::send($result);
    }

    public function testOpenAI()
    {
        $system_message = app()->make(OpenAI\Contracts\MessageContract::class);

        $system_message->setRole(OpenAI\Enums\MessageRole::SYSTEM);
        $system_message->setContent('You are helpful assistant');

        $user_message = app()->make(OpenAI\Contracts\MessageContract::class);

        $user_message->setRole(OpenAI\Enums\MessageRole::USER);
        $user_message->setContent('Say hello');

        $messages_collection = app()->make(OpenAI\Contracts\MessagesCollectionContract::class);
        $messages_collection->setMessages($system_message, $user_message);

        $completion = OpenAIFacade::createChatCompletion(
            OpenAI\Enums\Model::GPT_35_TURBO_16K,
            $messages_collection
        );

        $count_tokens = OpenAIFacade::countTokens(
            OpenAI\Enums\Model::GPT_35_TURBO_16K,
            $user_message->getContent()
        );

        return Response::send([
            'completion' => [
                'content' => $completion->getContent(),
                'completion_tokens' => $completion->getCompletionTokens(),
                'prompt_tokens' => $completion->getPromptTokens(),
                'total_tokens' => $completion->getTotalTokens(),
            ],
            'count_tokens' => $count_tokens,
        ]);
    }
}
