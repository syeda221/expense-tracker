<?php

namespace App\Services\AI;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqService implements AIServiceInterface
{
    private readonly string $apiKey;
    private readonly string $model;
    private readonly float $temperature;
    private readonly string $promptTemplate;

    public function __construct()
    {
        $this->apiKey = config('ai.providers.groq.api_key');
        $this->model = config('ai.providers.groq.model', 'llama-3.1-8b-instant');
        $this->temperature = config('ai.providers.groq.temperature', 0.1);
        $this->promptTemplate = config('ai.classifier.prompt');
    }

    public function classify(string $description): array
    {
        $prompt = str_replace(':description', $description, $this->promptTemplate);

        $response = $this->sendRequest($prompt);

        return $this->parseResponse($response, $description);
    }

    public function name(): string
    {
        return 'groq';
    }

    public function ask(string $prompt, float $temperature = 0.7, int $maxTokens = 1024): string
    {
        $url = 'https://api.groq.com/openai/v1/chat/completions';

        $response = Http::timeout(30)
            ->retry(1, 1000, throw: false)
            ->withToken($this->apiKey)
            ->post($url, [
                'model' => 'llama-3.1-8b-instant',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => $temperature,
                'max_tokens' => $maxTokens,
            ]);

        if ($response->failed()) {
            $status = $response->status();
            $body = $response->body();
            Log::error("Groq API ask request failed", [
                'status' => $status,
                'body' => $body,
            ]);
            throw new ConnectionException("Groq API returned status {$status}: {$body}");
        }

        $data = $response->json();
        $text = $data['choices'][0]['message']['content'] ?? '';

        return trim($text);
    }

    public function getAdvisorResponse(array $messages, float $temperature = 0.7, int $maxTokens = 1024): string
    {
        $url = 'https://api.groq.com/openai/v1/chat/completions';

        $response = Http::timeout(30)
            ->retry(1, 1000, throw: false)
            ->withToken($this->apiKey)
            ->post($url, [
                'model' => 'llama-3.1-8b-instant',
                'messages' => $messages,
                'temperature' => $temperature,
                'max_tokens' => $maxTokens,
            ]);

        if ($response->failed()) {
            $status = $response->status();
            $body = $response->body();
            Log::error("Groq API advisor request failed", [
                'status' => $status,
                'body' => $body,
            ]);
            throw new ConnectionException("Groq API returned status {$status}: {$body}");
        }

        $data = $response->json();
        return trim($data['choices'][0]['message']['content'] ?? '');
    }

    public function parseCopilotIntents(string $userInput, array $preferences = []): array
    {
        $prefsJson = empty($preferences) ? "None" : json_encode($preferences);
        $today = \Carbon\Carbon::now()->format('Y-m-d');
        $systemPrompt = <<<PROMPT
You are a financial AI Copilot intent parser. Analyze the user's input and extract ALL actions they want to perform.
Consider the user's preferences when categorizing (e.g. if preference maps 'Starbucks' to 'Coffee', use 'Coffee').
User Preferences: $prefsJson
Today's Date: $today (Use this to calculate relative deadlines like "in 7 days")

Return ONLY a valid JSON array of action objects. Do NOT wrap it in markdown. Do NOT return anything else.

Possible actions and their required fields:
1. {"action": "create_expense", "amount": float, "category": string, "merchant": string, "date": "YYYY-MM-DD"|null}
2. {"action": "manage_budget", "type": "set"|"add"|"remove"|"increase"|"decrease", "category": string|null (null for overall), "amount": float|null, "period": "monthly"|"weekly"|"yearly"} (Use ONLY for setting spending limits/budgets. DO NOT use for savings)
3. {"action": "manage_goal", "type": "create"|"update"|"delete", "name": string, "target_amount": float, "deadline": "YYYY-MM-DD"|null} (Use this when the user wants to "save" money or set a savings goal)
4. {"action": "set_preference", "key": string, "value": string}
5. {"action": "ask_insight", "question": string}
6. {"action": "make_recurring", "expense_id": int|null, "frequency": "monthly"|"weekly"}

If the input is just a general question or doesn't match above, use {"action": "ask_insight", "question": "..."}.
PROMPT;

        $response = $this->getAdvisorResponse([
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userInput]
        ], 0.1);
        
        $cleaned = trim($response);
        $cleaned = preg_replace('/^```(?:json)?\s*|\s*```$/', '', $cleaned);
        
        $decoded = json_decode($cleaned, true);
        
        if (!is_array($decoded)) {
            return [['action' => 'ask_insight', 'question' => $userInput]];
        }
        
        // Handle case where it returns a single object instead of an array
        if (isset($decoded['action'])) {
            return [$decoded];
        }

        return $decoded;
    }

    private function sendRequest(string $prompt): string
    {
        $url = 'https://api.groq.com/openai/v1/chat/completions';

        $response = Http::timeout(30)
            ->retry(2, 1000, throw: false)
            ->withToken($this->apiKey)
            ->post($url, [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => $this->temperature,
                'max_tokens' => 512,
            ]);

        if ($response->failed()) {
            $status = $response->status();
            $body = $response->body();
            Log::error("Groq API request failed", [
                'status' => $status,
                'body' => $body,
            ]);
            throw new ConnectionException("Groq API returned status {$status}: {$body}");
        }

        return $response->body();
    }

    private function parseResponse(string $rawBody, string $description): array
    {
        $data = json_decode($rawBody, true);

        $text = $data['choices'][0]['message']['content'] ?? null;

        if (!$text) {
            throw new \RuntimeException('No text content in Groq response');
        }

        $cleaned = trim($text);
        $cleaned = preg_replace('/^```(?:json)?\s*|\s*```$/', '', $cleaned);

        $parsed = json_decode($cleaned, true);

        if (!is_array($parsed) || !isset($parsed['category'])) {
            Log::warning('Failed to parse Groq classification response', [
                'raw' => $cleaned,
                'description' => $description,
            ]);
            throw new \RuntimeException('Invalid classification response format');
        }

        return [
            'category' => $parsed['category'] ?? config('ai.classifier.default_category', 'Other'),
            'merchant' => $parsed['merchant'] ?? null,
            'confidence' => (float) ($parsed['confidence'] ?? 0),
            'is_recurring' => (bool) ($parsed['is_recurring'] ?? false),
        ];
    }
}
