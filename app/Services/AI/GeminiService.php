<?php

namespace App\Services\AI;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService implements AIServiceInterface
{
    private readonly string $apiKey;
    private readonly string $model;
    private readonly float $temperature;
    private readonly string $promptTemplate;

    public function __construct()
    {
        $this->apiKey = config('ai.providers.gemini.api_key');
        $this->model = config('ai.providers.gemini.model', 'gemini-2.5-flash');
        $this->temperature = config('ai.providers.gemini.temperature', 0.1);
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
        return 'gemini';
    }

    private function sendRequest(string $prompt): string
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";

        $response = Http::timeout(30)
            ->retry(2, 1000, throw: false)
            ->withQueryParameters(['key' => $this->apiKey])
            ->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => $this->temperature,
                    'maxOutputTokens' => 256,
                ],
            ]);

        if ($response->failed()) {
            $status = $response->status();
            $body = $response->body();
            Log::error("Gemini API request failed", [
                'status' => $status,
                'body' => $body,
            ]);
            throw new ConnectionException("Gemini API returned status {$status}: {$body}");
        }

        return $response->body();
    }

    private function parseResponse(string $rawBody, string $description): array
    {
        $data = json_decode($rawBody, true);

        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$text) {
            throw new \RuntimeException('No text content in Gemini response');
        }

        $cleaned = trim($text);
        $cleaned = preg_replace('/^```(?:json)?\s*|\s*```$/', '', $cleaned);

        $parsed = json_decode($cleaned, true);

        if (!is_array($parsed) || !isset($parsed['category'])) {
            Log::warning('Failed to parse Gemini classification response', [
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
