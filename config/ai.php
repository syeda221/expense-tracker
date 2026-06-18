<?php

return [
    'default' => env('AI_PROVIDER', 'gemini'),

    'providers' => [
        'gemini' => [
            'api_key' => env('GOOGLE_GEMINI_API_KEY'),
            'model' => env('GOOGLE_GEMINI_MODEL', 'gemini-2.5-flash'),
            'temperature' => 0.1,
            'max_retries' => 3,
        ],
        'groq' => [
            'api_key' => env('GROQ_API_KEY'),
            'model' => env('GROQ_MODEL', 'llama3-70b-8192'),
            'temperature' => 0.1,
            'max_retries' => 3,
        ],
    ],

    'classifier' => [
        'prompt' => 'You are an expense classifier. Given an expense description, classify it into exactly one of these categories: Food & Dining, Shopping, Transport, Fuel, Groceries, Bills, Utilities, Healthcare, Education, Entertainment, Travel, Rent, Investment, Salary, Subscription, Other.

Respond ONLY with a valid JSON object (no markdown, no code fences) using this exact structure:
{
    "category": "string",
    "merchant": "string or null",
    "confidence": float between 0 and 1,
    "is_recurring": true or false
}

Description: ":description"',
        'default_category' => 'Other',
        'fallback_confidence' => 0,
    ],
];
