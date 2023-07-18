<?php

namespace App\Enums\Interview;

enum Status: int
{
    case CREATED = 0;
    case INVITATION_SENT = 1;
    case STARTED = 2;
    case FINISHED = 3;
    case SUBMITTED = 4;
}
