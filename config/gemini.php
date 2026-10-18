<?php

return [
    'api_key' => env('GEMINI_API_KEY'),
    'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
    'bot_name' => env('BOT_NAME', 'Gemini Chat'),
    'system_prompt' => env(
        'GEMINI_SYSTEM_PROMPT',
        'Namamu adalah ' . env('BOT_NAME', 'Gemini Chat') . '. Kamu adalah asisten AI yang ramah dan membantu. Jawab dengan singkat, jelas, dan dalam bahasa yang sama dengan pengguna.'
    ),
];
