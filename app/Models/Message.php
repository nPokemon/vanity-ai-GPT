<?php

namespace App\Models;

use App\Facades\OpenAI;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'interview_id',
        'role',
        'content',
        'tokens_count',
        'is_skipped'
    ];

    protected $casts = [
        'role' => OpenAI\Enums\MessageRole::class,
    ];

    public function scopeSystem(Builder $query, bool $system = true): Builder
    {
        if ($system) {
            return $query->where('role', OpenAI\Enums\MessageRole::SYSTEM);
        } else {
            return $query->where('role', '!=', OpenAI\Enums\MessageRole::SYSTEM);
        }
    }

    public function interview()
    {
        return $this->belongsTo(Interview::class);
    }
}
