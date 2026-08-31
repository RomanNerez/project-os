<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Integration Section AiAgent Container
    |--------------------------------------------------------------------------
    |
    |
    |
    */

    'default' => env('LLPHANT_PROVIDER', 'openrouter'),

    'providers' => [
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        ],
        
        'openrouter' => [
            'api_key' => env('OPENROUTER_API_KEY'),
            'base_url' => 'https://openrouter.ai/api/v1',
            'model' => env('OPENROUTER_MODEL', 'meta-llama/llama-3.3-70b-instruct'),
        ],

        'gemini' => [
            'api_key' => env('GEMINI_API_KEY'),
            'model' => env('GEMINI_MODEL', 'gemini-3.6-flash'),
        ],
    ],

    'tools' => [
        \App\Containers\Integration\AiAgent\Tools\CreateProjectTaskTool::class,
    ],
];
