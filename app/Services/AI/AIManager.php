<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;

class AIManager
{
    private ?AIServiceInterface $driver = null;

    public function __construct(
        private readonly array $providers = []
    ) {}

    public function driver(?string $name = null): AIServiceInterface
    {
        $name ??= config('ai.default', 'gemini');

        if ($this->driver !== null) {
            return $this->driver;
        }

        $this->driver = match ($name) {
            'gemini' => app(GeminiService::class),
            'groq' => app(GroqService::class),
            default => throw new \InvalidArgumentException("AI provider [{$name}] is not supported."),
        };

        return $this->driver;
    }

    public function classify(string $description): array
    {
        try {
            $result = $this->driver()->classify($description);

            Log::info('AI classification completed', [
                'description' => $description,
                'result' => $result,
                'provider' => $this->driver()->name(),
            ]);

            return $result;
        } catch (\Throwable $e) {
            Log::error('AI classification failed, using fallback', [
                'description' => $description,
                'error' => $e->getMessage(),
                'provider' => $this->driver()->name(),
            ]);

            return [
                'category' => config('ai.classifier.default_category', 'Other'),
                'merchant' => null,
                'confidence' => config('ai.classifier.fallback_confidence', 0),
                'is_recurring' => false,
            ];
        }
    }
}
