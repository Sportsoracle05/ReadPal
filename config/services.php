<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */
    
    // ── Google Gemini ─────────────────────────────────────────
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),

        // gemini-1.5-flash   → fast, cheap, free tier available ✅ (recommended)
        // gemini-1.5-pro     → more capable, fewer free requests
        // gemini-1.0-pro     → older, slightly cheaper
        'model'   => env('GEMINI_MODEL', 'gemini-1.5-flash'),

        'timeout' => env('GEMINI_TIMEOUT', 15), // seconds
    ],

    // ── Groq (Fast LLaMA / Mixtral) ──────────────────────────
    'groq' => [
        'api_key' => env('GROQ_API_KEY'),

        // llama-3.1-8b-instant   → fastest, free, good quality ✅ (recommended)
        // llama-3.1-70b-versatile → better quality, fewer free tokens
        // mixtral-8x7b-32768      → long context window (32K tokens)
        // gemma2-9b-it            → Google's Gemma via Groq
        'model'   => env('GROQ_MODEL', 'llama-3.1-8b-instant'),

        'timeout' => env('GROQ_TIMEOUT', 10),
    ],

    // ── OpenRouter (Multi-model gateway) ─────────────────────
    'openrouter' => [
        'api_key' => env('OPENROUTER_API_KEY'),

        // Primary model to try
        'model'   => env('OPENROUTER_MODEL', 'deepseek/deepseek-chat'),

        // Models tried in sequence if primary fails
        'fallback_models' => [
            env('OPENROUTER_MODEL', 'deepseek/deepseek-chat'),
            'meta-llama/llama-3.1-8b-instruct:free',
            'mistralai/mistral-7b-instruct:free',
        ],

        'timeout' => env('OPENROUTER_TIMEOUT', 20),
    ],

    // ── HuggingFace Inference API ─────────────────────────────
    'huggingface' => [
        'api_key' => env('HUGGINGFACE_API_KEY'),

        // Instruction-following models only (base models won't follow prompts)
        'model'   => env('HUGGINGFACE_MODEL', 'mistralai/Mistral-7B-Instruct-v0.2'),

        'timeout' => env('HUGGINGFACE_TIMEOUT', 30), // HF can be slow (cold starts)
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],
    
    'openai' => [
        'key' => env('OPENAI_API_KEY'),
    ],


    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
