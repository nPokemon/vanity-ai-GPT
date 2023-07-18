<?php

namespace App\Models;

use App\Enums;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'slug',
        'title',
        'description',
        'ai_personality',
        'ai_instructions',
        'start_message',
        'end_message',
        'ai_settings',
        'status',
        'total_tokens_count',
        'invitation_sent_at',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'ai_settings' => 'array',
        'status' => Enums\Interview\Status::class,
        'invitation_sent_at' => 'datetime',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => Enums\Interview\Status::CREATED,
        'ai_settings' => '{}',
    ];

    public function isStarted(bool $started = true)
    {
        if ($started) {
            return $this->status->value >= Enums\Interview\Status::STARTED->value;
        } else {
            return $this->status->value < Enums\Interview\Status::STARTED->value;
        }
    }

    public function isInvitationSent(bool $sent = true)
    {
        if ($sent) {
            return $this->status->value >= Enums\Interview\Status::INVITATION_SENT->value;
        } else {
            return $this->status->value < Enums\Interview\Status::INVITATION_SENT->value;
        }
    }

    public function isFinished(bool $finished = true)
    {
        if ($finished) {
            return $this->status->value >= Enums\Interview\Status::FINISHED->value;
        } else {
            return $this->status->value < Enums\Interview\Status::FINISHED->value;
        }
    }
    public function isSubmitted(bool $submitted = true)
    {
        if ($submitted) {
            return $this->status->value >= Enums\Interview\Status::SUBMITTED->value;
        } else {
            return $this->status->value < Enums\Interview\Status::SUBMITTED->value;
        }
    }

    public function interviewee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
