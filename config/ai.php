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
            'model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
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

    'search' => [
        'prompt' => 'You are an expense search assistant. Convert a natural language query into structured JSON parameters for filtering expenses.

Valid categories: Food & Dining, Shopping, Transport, Fuel, Groceries, Bills, Utilities, Healthcare, Education, Entertainment, Travel, Rent, Investment, Salary, Subscription, Other
Valid payment methods: Cash, Credit Card, Debit Card, UPI, Bank Transfer
Valid sort fields: expense_date, amount, created_at
Valid order values: asc, desc

Today is :date.

Rules:
- If query asks about "this month", set start_date to first day of current month, end_date to last day
- If query asks about "last month", set start_date to first day of previous month, end_date to last day
- If query asks about "this year", set start_date to January 1 of current year, end_date to December 31
- Set is_recurring to true if the query mentions recurring, subscription, monthly bill, renewal
- Set sort to "amount" and order to "desc" for queries about highest/most expensive

Respond ONLY with valid JSON (no markdown, no code fences, no explanations) using this exact structure:
{
    "category": null or string matching a valid category,
    "merchant": null or string,
    "payment_method": null or string matching a valid payment method,
    "start_date": null or "YYYY-MM-DD",
    "end_date": null or "YYYY-MM-DD",
    "min_amount": null or number,
    "max_amount": null or number,
    "is_recurring": null or true or false,
    "sort": "expense_date",
    "order": "desc"
}

Query: ":query"',
        'cache_ttl' => 3600,
    ],
];
