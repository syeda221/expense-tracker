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
        $this->model = config('ai.providers.groq.model', 'llama3-70b-8192');
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
