<?php

return [
    'x_api_key' => env('ENGLISH_API_KEY'),

    'listening' => [
        'gen_quizz_endpoint' => env('ENGLISH_LISTENING_GEN_QUIZZ_ENDPOINT'),
        'random_endpoint' => env('ENGLISH_LISTENING_RANDOM_ENDPOINT'),
        'gen_quizz_limit' => env('ENGLISH_LISTENING_GEN_QUIZZ_LIMIT', 10),
    ],

    'reading' => [
        'gen_quizz_limit' => env('ENGLISH_READING_GEN_QUIZZ_LIMIT', 10),
        'gen_topic_endpoint' => env('ENGLISH_READING_GEN_TOPIC_ENDPOINT'),
        'gen_quizz_endpoint' => env('ENGLISH_READING_GEN_QUIZZ_ENDPOINT'),
    ],

    'writing' => [
        'gen_quizz_limit' => env('ENGLISH_WRITING_GEN_QUIZZ_LIMIT', 10),
    ],
];

