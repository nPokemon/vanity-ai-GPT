<?php

namespace App\Providers;

use App\Facades\OpenAI;
use Illuminate\Support\ServiceProvider;

class OpenAIServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        if (config('services.openai.testing')) {

            $this->app->bind('OpenAIAccessor', function () {
                return $this->app->make(OpenAI\Test\TestAccessor::class);
            });

            $this->app->bind(OpenAI\Contracts\MessageContract::class, OpenAI\Test\Message::class);
            $this->app->bind(OpenAI\Contracts\MessagesCollectionContract::class, OpenAI\Test\MessagesCollection::class);

        } else {

            $this->app->bind('OpenAIAccessor', function () {
                return $this->app->make(OpenAI\V1\V1Accessor::class);
            });

            $this->app->bind(OpenAI\Contracts\MessageContract::class, OpenAI\V1\Message::class);
            $this->app->bind(OpenAI\Contracts\MessagesCollectionContract::class, OpenAI\V1\MessagesCollection::class);

        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
