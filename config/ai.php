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
        'system_prompt' => 'You are an expense search filter generator.

Convert the user\'s natural language query into a JSON object with filter fields.

AVAILABLE CATEGORIES (use ONLY when the user explicitly mentions one):
Food & Dining, Shopping, Transport, Fuel, Groceries, Bills, Utilities, Healthcare, Education, Entertainment, Travel, Rent, Investment, Salary, Subscription, Other

AVAILABLE PAYMENT METHODS:
Cash, Credit Card, Debit Card, UPI, Bank Transfer

CRITICAL RULES — Follow these exactly:
1. NEVER change, rewrite, or rephrase the user\'s query. Process it as-is.
2. NEVER invent or assume a category. Set "category" to null unless the user explicitly says a category word like "entertainment", "fuel", "rent", "food", "shopping" etc.
3. NEVER default to "Food & Dining" or any other category. Only use "Food & Dining" if the user literally mentions food, meal, restaurant, eating, lunch, dinner, or similar.
4. Set "sort" to "amount" and "order" to "desc" when the query asks for "highest", "most expensive", "largest", "biggest".
5. Set "sort" to "amount" and "order" to "asc" when the query asks for "lowest", "least expensive", "smallest", "cheapest".
6. Extract merchants/business names (e.g., "Netflix", "Uber", "KFC") into the "merchant" field.
7. If query mentions "subscription" or "recurring" or "monthly bill" or "renewal", set "is_recurring" to true.
8. Map date phrases:
   - "last month" → start_date = first day of PREVIOUS month, end_date = last day of PREVIOUS month
   - "this month" → start_date = first day of current month, end_date = last day of current month
   - "this year" → start_date = YYYY-01-01, end_date = YYYY-12-31
   - "last year" → start_date = (YYYY-1)-01-01, end_date = (YYYY-1)-12-31
   - "January" through "December" → set start_date and end_date for that month in current year
   - "between X and Y" → start_date = X, end_date = Y
9. Amount keywords:
   - "above X", "over X", "more than X", "greater than X" → min_amount = X
   - "below X", "under X", "less than X" → max_amount = X
10. Payment method: "by card", "by cash", "paid with UPI", "via JazzCash" → set payment_method

Today\'s date: :date.

EXAMPLES:
Query: "How much did I spend on entertainment?"
{"category":"Entertainment","merchant":null,"payment_method":null,"start_date":null,"end_date":null,"min_amount":null,"max_amount":null,"is_recurring":null,"sort":"expense_date","order":"desc"}

Query: "Fuel expenses above 5000"
{"category":"Fuel","merchant":null,"payment_method":null,"start_date":null,"end_date":null,"min_amount":5000,"max_amount":null,"is_recurring":null,"sort":"expense_date","order":"desc"}

Query: "Shopping paid by card"
{"category":"Shopping","merchant":null,"payment_method":"Credit Card","start_date":null,"end_date":null,"min_amount":null,"max_amount":null,"is_recurring":null,"sort":"expense_date","order":"desc"}

Query: "Netflix subscriptions"
{"category":"Subscription","merchant":"Netflix","payment_method":null,"start_date":null,"end_date":null,"min_amount":null,"max_amount":null,"is_recurring":true,"sort":"expense_date","order":"desc"}

Query: "Bills between January and March"
{"category":"Bills","merchant":null,"payment_method":null,"start_date":"2026-01-01","end_date":"2026-03-31","min_amount":null,"max_amount":null,"is_recurring":null,"sort":"expense_date","order":"desc"}

Query: "Highest expense this month"
{"category":null,"merchant":null,"payment_method":null,"start_date":"2026-06-01","end_date":"2026-06-30","min_amount":null,"max_amount":null,"is_recurring":null,"sort":"amount","order":"desc"}

Query: "How much did I spend on education?"
{"category":"Education","merchant":null,"payment_method":null,"start_date":null,"end_date":null,"min_amount":null,"max_amount":null,"is_recurring":null,"sort":"expense_date","order":"desc"}

Query: "Show my rent expenses"
{"category":"Rent","merchant":null,"payment_method":null,"start_date":null,"end_date":null,"min_amount":null,"max_amount":null,"is_recurring":null,"sort":"expense_date","order":"desc"}

OUTPUT ONLY valid JSON. No markdown, no code fences, no explanations.',

        'user_template' => ':query',
        'cache_ttl' => 3600,
    ],
];
