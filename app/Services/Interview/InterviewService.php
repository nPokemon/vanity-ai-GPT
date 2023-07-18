<?php

namespace App\Services\Interview;

use App;
use App\Enums;
use App\Facades\OpenAI;
use App\Facades\OpenAIFacade;
use App\Models;
use App\Repositories;
use App\Services\AbstractService;
use Illuminate\Pagination\LengthAwarePaginator;
use Response;
use URL;

class InterviewService extends AbstractService
{
    public const MODEL = OpenAI\Enums\Model::GPT_35_TURBO_16K;

    public const MAX_MODEL_TOKENS_COUNT = 16384;

    public const MAX_MODEL_COMPLETION_TOKENS_COUNT = 300;

    public function __construct(
        private Repositories\InterviewRepository $interview_repository,
        private Repositories\UserRepository $user_repository,
        private Repositories\MessageRepository $message_repository
    ) {
        //
    }

    public function setInterviewInvitationHasSent(Models\Interview $interview): void
    {
        $this->interview_repository->update($interview, [
            'status' => Enums\Interview\Status::INVITATION_SENT,
            'invitation_sent_at' => now(),
        ]);
    }

    public function makeInviteUrl(Models\Interview $interview): string
    {
        $token = $this->user_repository->createToken($interview->interviewee)->plainTextToken;

        return URL::signedRoute('interviews.show', [
            'interview' => $interview->id,
            'token' => $token,
        ]);
    }

    public function startInterview(Models\Interview $interview)
    {
        abort_if(
            $interview->isStarted() && ! $interview->isFinished(),
            Response::BAD_REQUEST,
            __('Interview has been already started')
        );

        abort_if(
            $interview->isFinished(),
            Response::BAD_REQUEST,
            __('Interview has been already finished')
        );

        $system_message_content = $this->collectSystemMessage($interview);

        $messages = [
            $this->makeMessage(OpenAI\Enums\MessageRole::SYSTEM, $system_message_content),
            $this->makeMessage(OpenAI\Enums\MessageRole::USER, $interview->start_message),
        ];

        $this->message_repository->create([
            'interview_id' => $interview->id,
            'role' => OpenAI\Enums\MessageRole::SYSTEM,
            'content' => $system_message_content,
            'tokens_count' => OpenAIFacade::countTokens(self::MODEL, $system_message_content),
        ]);

        $messages_collection = $this->makeMessagesCollection(...$messages);

        $open_ai_completion = OpenAIFacade::createChatCompletion(self::MODEL, $messages_collection);

        $message = $this->makeMessageFromOpenAICompletion($interview, $open_ai_completion);

        $this->setInterviewHasStarted($interview);

        return $message;
    }

    private function collectSystemMessage(Models\Interview $interview): string
    {
        return $interview->ai_personality."\n".$interview->ai_instructions;
    }

    private function makeMessage(OpenAI\Enums\MessageRole $role, string $content): OpenAI\Contracts\MessageContract
    {
        $message = App::make(OpenAI\Contracts\MessageContract::class);
        $message->setRole($role);
        $message->setContent($content);

        return $message;
    }

    private function makeMessagesCollection(OpenAI\Contracts\MessageContract ...$messages): OpenAI\Contracts\MessagesCollectionContract
    {
        $messages_collection = App::make(OpenAI\Contracts\MessagesCollectionContract::class);
        $messages_collection->setMessages(...$messages);

        return $messages_collection;
    }

    private function makeMessageFromOpenAICompletion(Models\Interview $interview, OpenAI\Contracts\ChatCompletionContract $completion): Models\Message
    {
        return $this->message_repository->create([
            'interview_id' => $interview->id,
            'role' => OpenAI\Enums\MessageRole::ASSISTANT,
            'content' => $completion->getContent(),
            'tokens_count' => $completion->getCompletionTokens(),
        ]);
    }

    private function setInterviewHasStarted(Models\Interview $interview): void
    {
        $this->interview_repository->update($interview, [
            'status' => Enums\Interview\Status::STARTED,
            'started_at' => now(),
        ]);
    }

    private function setInterviewHasFinished(Models\Interview $interview): void
    {
        $this->interview_repository->update($interview, [
            'status' => Enums\Interview\Status::FINISHED,
            'finished_at' => now(),
        ]);
    }
    private function setInterviewHasSubmitted(Models\Interview $interview): void
    {
        $this->interview_repository->update($interview, [
            'status' => Enums\Interview\Status::SUBMITTED
        ]);
    }

    public function endInterview(Models\Interview $interview): Models\Message
    {
        abort_if(
            ! $interview->isStarted(),
            Response::BAD_REQUEST,
            __('Interview has not started yet')
        );

        abort_if(
            $interview->isFinished(),
            Response::BAD_REQUEST,
            __('Interview has finished already')
        );

        $end_message = $this->makeMessage(OpenAI\Enums\MessageRole::USER, $interview->end_message);

        $messages_collection = $this->makeMessagesCollection($end_message);

        $open_ai_completion = OpenAIFacade::createChatCompletion(self::MODEL, $messages_collection);

        $message = $this->makeMessageFromOpenAICompletion($interview, $open_ai_completion);

        $this->setInterviewHasFinished($interview);

        return $message;
    }

    public function submitInterview(Models\Interview $interview): bool
    {
        abort_if(
            !$interview->isStarted(),
            Response::BAD_REQUEST,
            __('Interview has not started yet')
        );

        abort_if(
            !$interview->isFinished(),
            Response::BAD_REQUEST,
            __('Interview has not finished yet')
        );

        $this->setInterviewHasSubmitted($interview);

        return true;
    }

    public function sendMessage(Models\Interview $interview, string $content): array
    {
        abort_if(
            $this->availableTokens($interview, $content) <= 0,
            Response::BAD_REQUEST,
            __('Maximum number of tokens exceeded')
        );

        abort_if(
            $interview->isFinished(),
            Response::BAD_REQUEST,
            __('Interview has been already finished')
        );

        $user_message = $this->message_repository->create([
            'interview_id' => $interview->id,
            'role' => OpenAI\Enums\MessageRole::USER,
            'content' => $content,
            'tokens_count' => OpenAIFacade::countTokens(self::MODEL, $content),
        ]);

        $messages = [];
        foreach ($this->message_repository->getInterviewMessages($interview) as $interview_message) {
            $messages[] = $this->makeMessage($interview_message->role, $interview_message->content);
        }

        $messages_collection = $this->makeMessagesCollection(...$messages);

        $chat_completion = OpenAIFacade::createChatCompletion(self::MODEL, $messages_collection);

        $chat_message = $this->makeMessageFromOpenAICompletion($interview, $chat_completion);

        $this->updateInterviewTotalTokens($interview, $chat_completion);

        return [
            $user_message,
            $chat_message
        ];
    }

    private function availableTokens(Models\Interview $interview, string $content)
    {
        $user_message_tokens_count = OpenAIFacade::countTokens(self::MODEL, $content);

        $balance =
            self::MAX_MODEL_TOKENS_COUNT -
            self::MAX_MODEL_COMPLETION_TOKENS_COUNT -
            $user_message_tokens_count -
            $interview->total_tokens_count;

        return $balance;
    }

    private function updateInterviewTotalTokens(Models\Interview $interview, OpenAI\Contracts\ChatCompletionContract $completion): void
    {
        $this->interview_repository->update($interview, [
            'total_tokens_count' => $completion->getTotalTokens(),
        ]);
    }
    private function decrementInterviewTotalTokens(Models\Interview $interview, int $tokens_count): void
    {
        $this->interview_repository->update($interview, [
            'total_tokens_count' => $interview->total_tokens_count - $tokens_count
        ]);
    }

    public function getMessages(Models\Interview $interview): LengthAwarePaginator
    {
        $messages = $this->message_repository->getInterviewMessages($interview, 'asc', true);

        return $messages;
    }

    public function deleteMessage(Models\Interview $interview, Models\Message $message): bool
    {
        abort_if(
            $message->role != OpenAI\Enums\MessageRole::USER,
            Response::BAD_REQUEST,
            __('Unable to delete the message')
        );

        $this->message_repository->delete($message);

        $assistants_message = $this->message_repository->getInterviewMessages($interview, 'DESC')->where('id', '<', $message->id)->first();
        $this->message_repository->delete($assistants_message);

        $tokens_count_to_decrement = $message->tokens_count + $assistants_message->tokens_count;

        $this->decrementInterviewTotalTokens($interview, $tokens_count_to_decrement);

        return true;
    }

    public function skipMessage(Models\Interview $interview)
    {
        $message = $this->message_repository->getInterviewMessages($interview, 'DESC')->first();

        abort_if(
            !$message,
            Response::BAD_REQUEST,
            __('No messages available for skip')
        );

        abort_if(
            $message->role != OpenAI\Enums\MessageRole::ASSISTANT,
            Response::BAD_REQUEST,
            __('Unable to skip the message')
        );

        abort_if(
            !$interview->isStarted(),
            Response::BAD_REQUEST,
            __('Interview has not started yet')
        );

        abort_if(
            $interview->isFinished(),
            Response::BAD_REQUEST,
            __('Interview has been already finished')
        );

        $skip_message_prompt = 'I would like to skip this question. Please generate a new question that is different from the previous one.';

        $prompt_tokens = OpenAIFacade::countTokens(self::MODEL, $skip_message_prompt);

        $messages = [];
        foreach ($this->message_repository->getInterviewMessages($interview) as $interview_message) {
            $messages[] = $this->makeMessage($interview_message->role, $interview_message->content);
        }

        $messages[] = $this->makeMessage(OpenAI\Enums\MessageRole::USER, $skip_message_prompt);

        $messages_collection = $this->makeMessagesCollection(...$messages);

        $chat_completion = OpenAIFacade::createChatCompletion(self::MODEL, $messages_collection);

        $completion_message = $this->makeMessageFromOpenAICompletion($interview, $chat_completion);

        $this->message_repository->update($message, [
            'is_skipped' => true
        ]);

        $this->updateInterviewTotalTokens($interview, $chat_completion);

        $this->decrementInterviewTotalTokens($interview, $message->tokens_count + $prompt_tokens);

        return $completion_message;
    }
}
