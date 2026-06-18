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
        'prompt' => 'You are an expense classifier. Given an expense description, extract the category and merchant. Return ONLY valid JSON.

Categories: Food & Dining, Shopping, Transport, Fuel, Groceries, Bills, Utilities, Healthcare, Education, Entertainment, Travel, Rent, Investment, Salary, Subscription, Other

MERCHANT EXTRACTION RULES:
- If a business/company/person name is mentioned, return it as the merchant.
- If no explicit name, infer the most likely merchant from context (e.g., "school fee" -> "School", "electricity bill" -> "Electricity Provider", "bus fare" -> "Transport", "medicine from pharmacy" -> infer the pharmacy type).
- NEVER leave merchant null if context provides a clue. Only null if truly impossible.
- Return the merchant name exactly as mentioned, do not modify it.

Confidence: >0.8 if category is clear, 0.5-0.8 if uncertain, <0.5 if guessing.

Examples:
"Paid tuition at ABC School" -> {"category":"Education","merchant":"ABC School","confidence":0.95,"is_recurring":false}
"Electricity bill" -> {"category":"Bills","merchant":"Electricity Provider","confidence":0.9,"is_recurring":true}
"Netflix monthly subscription" -> {"category":"Subscription","merchant":"Netflix","confidence":0.98,"is_recurring":true}
"Bought medicine from City Pharmacy" -> {"category":"Healthcare","merchant":"City Pharmacy","confidence":0.95,"is_recurring":false}
"Uber ride to downtown" -> {"category":"Transport","merchant":"Uber","confidence":0.95,"is_recurring":false}
"Paid rent to Ali" -> {"category":"Rent","merchant":"Ali","confidence":0.9,"is_recurring":true}
"Daraz shopping" -> {"category":"Shopping","merchant":"Daraz","confidence":0.95,"is_recurring":false}

JSON: {"category":"...","merchant":"...","confidence":0.0-1.0,"is_recurring":true|false}

Description: ":description"',
        'default_category' => 'Other',
        'fallback_confidence' => 0,
    ],

    'search' => [
        'prompt' => 'Extract structured expense search filters from this query. Return ONLY a JSON object, no other text.

CATEGORIES: Food & Dining, Shopping, Transport, Fuel, Groceries, Bills, Utilities, Healthcare, Education, Entertainment, Travel, Rent, Investment, Salary, Subscription, Other
PAYMENT: Cash, Credit Card, Debit Card, UPI, Bank Transfer

RULES:
- Read the user query EXACTLY as written. Do not change it. Do not guess. Do not use examples.
- Set "category" to null unless the user EXPLICITLY names one category word. Never default to Food & Dining.
- "highest"/"largest"/"most expensive" -> sort=amount, order=desc
- "lowest"/"cheapest"/"smallest" -> sort=amount, order=asc
- "last month" -> start_date=first day of prev month, end_date=last day of prev month
- "this month" -> start_date=first day of current month, end_date=last day of current month
- "this year" -> start_date=YYYY-01-01, end_date=YYYY-12-31
- "last year" -> start_date=(YYYY-1)-01-01, end_date=(YYYY-1)-12-31
- "January" through "December" -> that month in current year
- "between X and Y" -> start_date=X, end_date=Y
- "above"/"over"/"more than"/">" X -> min_amount=X
- "below"/"under"/"less than"/"<" X -> max_amount=X
- "subscription"/"recurring"/"monthly bill"/"renewal" -> is_recurring=true
- "by card"/"by cash"/"paid with"/"via" -> payment_method=extracted method
- Extract merchant names from the query. For "Netflix subscriptions" -> merchant=Netflix AND is_recurring=true.
- Today: :date

OUTPUT JSON:
{"category":null|string,"merchant":null|string,"payment_method":null|string,"start_date":null|"YYYY-MM-DD","end_date":null|"YYYY-MM-DD","min_amount":null|number,"max_amount":null|number,"is_recurring":null|boolean,"sort":"expense_date"|"amount"|"created_at","order":"asc"|"desc"}

QUERY: :query',
        'cache_ttl' => 3600,
    ],
];
