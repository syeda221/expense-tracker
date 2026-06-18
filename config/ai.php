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
        'prompt' => 'You classify expense descriptions into category + merchant. Return ONLY valid JSON.

CATEGORIES: Food & Dining, Shopping, Transport, Fuel, Groceries, Bills, Utilities, Healthcare, Education, Entertainment, Travel, Rent, Investment, Salary, Subscription, Other

MERCHANT EXTRACTION — Follow these rules STRICTLY:
1. If a specific business name, company, store, person, or institution is mentioned, return it EXACTLY as written.
2. If description starts with "paid" or "pay" followed by a noun (e.g. "paid school fee", "paid rent"), that noun is the merchant.
3. If description mentions a known brand/service (PTCL, KFC, Netflix, Uber, Daraz, Careem, Shell, etc.), return that brand name.
4. If no specific name but context is clear, infer a generic merchant (e.g., "electricity bill" -> "Electricity Provider", "bus fare" -> "Bus Service", "grocery shopping" -> "Supermarket", "school fee" -> "School", "tuition fee" -> "Tuition Center", "medicine" -> "Pharmacy", "fuel" -> "Petrol Station", "gas bill" -> "Gas Company", "internet bill" -> "ISP", "water bill" -> "Water Company", "rent" -> "Landlord").
5. NEVER return null. Only use "Unknown" as absolute last resort.
6. is_recurring=true for: bills, subscriptions, rent, tuition, memberships, insurance, EMIs.

Confidence: >0.8 if clear, 0.5-0.8 if uncertain.

Examples:
"Paid school fee" -> {"category":"Education","merchant":"School","confidence":0.9,"is_recurring":true}
"PTCL internet bill" -> {"category":"Bills","merchant":"PTCL","confidence":0.95,"is_recurring":true}
"Netflix monthly subscription" -> {"category":"Subscription","merchant":"Netflix","confidence":0.98,"is_recurring":true}
"Bought medicine from City Pharmacy" -> {"category":"Healthcare","merchant":"City Pharmacy","confidence":0.95,"is_recurring":false}
"Uber ride to downtown" -> {"category":"Transport","merchant":"Uber","confidence":0.95,"is_recurring":false}
"Paid rent to Ali" -> {"category":"Rent","merchant":"Ali","confidence":0.9,"is_recurring":true}
"Daraz shopping" -> {"category":"Shopping","merchant":"Daraz","confidence":0.95,"is_recurring":false}
"KFC lunch" -> {"category":"Food & Dining","merchant":"KFC","confidence":0.97,"is_recurring":false}
"Electricity bill" -> {"category":"Bills","merchant":"Electricity Provider","confidence":0.9,"is_recurring":true}
"Bus fare to office" -> {"category":"Transport","merchant":"Bus Service","confidence":0.85,"is_recurring":false}
"Paid tuition at ABC School" -> {"category":"Education","merchant":"ABC School","confidence":0.95,"is_recurring":true}
"Bought groceries from Carrefour" -> {"category":"Groceries","merchant":"Carrefour","confidence":0.95,"is_recurring":false}

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
