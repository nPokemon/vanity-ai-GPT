<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('title')->index();
            $table->text('description')->nullable();
            $table->text('ai_personality');
            $table->text('ai_instructions');
            $table->text('start_message');
            $table->text('end_message');
            $table->json('ai_settings');
            $table->tinyInteger('status')->index();
            $table->unsignedInteger('total_tokens_count')->default(0)->index();
            $table->timestamp('invitation_sent_at')->nullable()->index();
            $table->timestamp('started_at')->nullable()->index();
            $table->timestamp('finished_at')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};
