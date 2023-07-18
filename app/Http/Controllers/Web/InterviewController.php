<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Interview as InterviewRequests;
use App\Models;

class InterviewController extends Controller
{
    public function getInterview(InterviewRequests\ViewRequest $request, Models\Interview $interview)
    {
        $interview->load('interviewee');

        return view('interview.index', [
            'interview_id' => $interview->id,
            'interviewee_name' => $interview->interviewee->name,
            'interview_status' => $interview->status,
            'token' => $request->token,
        ]);
    }
}
