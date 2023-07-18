1. POST /api/interviews/{interviewId}/start - take data about interview from database

response:
{
    "content": "..." // Introduce bot, generate first question
}

2. POST /api/interviews/{interviewId}/message - transmit user input, process it, return the response
{
    "content": "..." // user input
}

response:
{
    "content": "..." // bot response
}

3. POST /api/interviews/{interviewId}/stop - finish interview, say goodbye

response:
{
    "content": "..." // last words from bot
}

Interview model structure
id - internal unique id (int)
slug - text id for seo-friendly url (string)
interviewee_name - interviewee name (string)
interviewee_email - interview email (string)
ai_instructions - text instructions for ai (text)
ai_settings - json
total_tokens_count - int

Messages
id - internal unique id (int)
interview_id - internal interview id
role - tiny integer (system, assistant, user)
content - text content
tokens_count - int