<?php

return [
    'x_api_key' => env('AWS_ACCESS_KEY_ID'),

    'listening' => [
        'gen_quizz_limit' => env('ENGLISH_LISTENING_GEN_QUIZZ_LIMIT', 10)
    ],

    'reading' => [
        'gen_quizz_limit' => env('ENGLISH_READING_GEN_QUIZZ_LIMIT', 10)
    ],

    'writing' => [
        'gen_quizz_limit' => env('ENGLISH_WRITING_GEN_QUIZZ_LIMIT', 10)
    ],
];

