<?php

namespace App\Services\AI;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AISearchService
{
    private readonly string $apiKey;
    private readonly string $model;
    private readonly float $temperature;
    private readonly string $promptTemplate;
    private readonly int $cacheTtl;
    private readonly string $promptVersion;

    private const ALLOWED_FIELDS = [
        'category', 'merchant', 'payment_method',
        'start_date', 'end_date',
        'min_amount', 'max_amount',
        'is_recurring', 'sort', 'order',
    ];

    private const VALID_CATEGORIES = [
        'Food & Dining', 'Shopping', 'Transport', 'Fuel', 'Groceries',
        'Bills', 'Utilities', 'Healthcare', 'Education', 'Entertainment',
        'Travel', 'Rent', 'Investment', 'Salary', 'Subscription', 'Other',
    ];

    private const VALID_PAYMENT_METHODS = [
        'Cash', 'Credit Card', 'Debit Card', 'UPI', 'Bank Transfer',
    ];

    private const VALID_SORT_FIELDS = ['expense_date', 'amount', 'created_at'];
    private const VALID_ORDER = ['asc', 'desc'];

    public function __construct()
    {
        $this->apiKey = config('ai.providers.groq.api_key');
        $this->model = config('ai.providers.groq.model', 'llama-3.3-70b-versatile');
        $this->temperature = 0.1;
        $this->promptTemplate = config('ai.search.prompt');
        $this->cacheTtl = config('ai.search.cache_ttl', 3600);
        $this->promptVersion = md5($this->promptTemplate);
    }

    public function parse(string $query): array
    {
        $cacheKey = 'ai_search_v3_' . $this->promptVersion . '_' . md5($query);

        Log::debug('[AISearch] Starting parse', ['original_query' => $query]);

        $result = Cache::remember($cacheKey, $this->cacheTtl, function () use ($query) {
            Log::debug('[AISearch] Cache miss, calling Groq', ['query' => $query]);
            return $this->callGroq($query);
        });

        Log::debug('[AISearch] Parse complete', [
            'query' => $query,
            'result' => $result,
        ]);

        return $result;
    }

    private function callGroq(string $query): array
    {
        $prompt = str_replace(
            [':query', ':date'],
            [$query, now()->toDateString()],
            $this->promptTemplate
        );

        Log::debug('[AISearch] Sending to Groq', [
            'query' => $query,
            'prompt_length' => strlen($prompt),
        ]);

        try {
            $rawBody = $this->sendRequest($prompt);

            Log::debug('[AISearch] Raw Groq response', [
                'query' => $query,
                'raw_body' => $rawBody,
            ]);

            $parsed = $this->parseResponse($rawBody, $query);

            Log::info('[AISearch] Success', [
                'query' => $query,
                'filters' => $parsed,
            ]);

            return $parsed;
        } catch (\Throwable $e) {
            Log::warning('[AISearch] Failed, falling back to keyword search', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    private function sendRequest(string $prompt): string
    {
        $response = Http::timeout(30)
            ->retry(2, 1000, throw: false)
            ->withToken($this->apiKey)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => $this->temperature,
                'max_tokens' => 640,
            ]);

        if ($response->failed()) {
            $status = $response->status();
            $body = $response->body();
            Log::error('[AISearch] Groq API request failed', [
                'status' => $status,
                'body' => $body,
            ]);
            throw new ConnectionException("Groq search API returned status {$status}: {$body}");
        }

        return $response->body();
    }

    private function parseResponse(string $rawBody, string $query): array
    {
        $data = json_decode($rawBody, true);
        $text = $data['choices'][0]['message']['content'] ?? null;

        if (!$text) {
            Log::warning('[AISearch] No text content in Groq response', [
                'query' => $query,
                'raw' => $rawBody,
            ]);
            throw new \RuntimeException('No text content in Groq search response');
        }

        $cleaned = trim($text);
        $cleaned = preg_replace('/^```(?:json)?\s*|\s*```$/', '', $cleaned);

        Log::debug('[AISearch] Cleaned Groq response', [
            'query' => $query,
            'cleaned' => $cleaned,
        ]);

        $parsed = json_decode($cleaned, true);

        if (!is_array($parsed)) {
            Log::warning('[AISearch] Failed to parse Groq response as JSON', [
                'query' => $query,
                'cleaned' => $cleaned,
            ]);
            throw new \RuntimeException('Invalid search response format');
        }

        return $this->validateFilters($parsed, $query);
    }

    private function validateFilters(array $filters, string $query): array
    {
        $validated = [];

        foreach ($filters as $key => $value) {
            if (!in_array($key, self::ALLOWED_FIELDS, true)) {
                Log::debug('[AISearch] Ignoring unknown field', [
                    'query' => $query,
                    'field' => $key,
                ]);
                continue;
            }

            if ($value === null || $value === '') {
                continue;
            }

            $validated[$key] = match ($key) {
                'category' => in_array($value, self::VALID_CATEGORIES, true) ? $value : null,
                'payment_method' => in_array($value, self::VALID_PAYMENT_METHODS, true) ? $value : null,
                'merchant' => is_string($value) ? $value : null,
                'start_date', 'end_date' => $this->validateDate($value),
                'min_amount', 'max_amount' => is_numeric($value) ? (float) $value : null,
                'is_recurring' => filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                'sort' => in_array($value, self::VALID_SORT_FIELDS, true) ? $value : null,
                'order' => in_array($value, self::VALID_ORDER, true) ? $value : null,
                default => $value,
            };

            if ($validated[$key] === null || $validated[$key] === '') {
                unset($validated[$key]);
            }
        }

        Log::debug('[AISearch] Validated filters', [
            'query' => $query,
            'raw_from_ai' => $filters,
            'validated' => $validated,
        ]);

        return $validated;
    }

    private function validateDate(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $ts = strtotime($value);

        if ($ts === false) {
            return null;
        }

        return date('Y-m-d', $ts);
    }
}
