<?php

namespace App\Repositories;

use App\Models;

class MessageRepository extends AbstractRepository
{
    public function getInterviewMessages(Models\Interview $interview, string $order = 'asc', bool $paginated = false)
    {
        $messages = $interview->messages()->system(false)->where('is_skipped', false)->orderBy('created_at', $order)->orderBy('id', $order);

        return $paginated ? $messages->paginate(1000) : $messages->get();
    }
}
