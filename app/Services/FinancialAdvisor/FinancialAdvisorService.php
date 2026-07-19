<?php

namespace App\Services\FinancialAdvisor;

use App\Services\BudgetService;
use App\Services\AI\GroqService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FinancialAdvisorService
{
    private const CACHE_TTL = 86400; // 24 hours

    public function __construct(
        private readonly IntentDetector $intentDetector,
        private readonly BudgetService $budgetService,
        private readonly GroqService $groqService,
    ) {}

    public function process(int $userId, string $message): array
    {
        $detected = $this->intentDetector->detect($message);

        return match ($detected['intent']) {
            'budget_set' => $this->handleBudgetSet($userId, $detected['params']),
            'budget_status' => $this->handleBudgetStatus($userId, $detected['params']),
            'simulation' => $this->handleSimulation($userId, $detected['params']),
            'advice' => $this->handleAdvice($userId, $message),
            default => $this->handleUnknown($message),
        };
    }

    private function handleBudgetSet(int $userId, array $params): array
    {
        if (!$params['amount']) {
            return [
                'response' => "I couldn't detect an amount. Try something like: \"Set food budget to 5000\"",
                'type' => 'budget',
            ];
        }

        $result = $this->budgetService->setBudget(
            $userId,
            $params['amount'],
            $params['category'],
            $params['period_type'] ?? 'monthly',
        );

        return [
            'response' => $result['message'],
            'type' => 'budget',
            'data' => $result,
        ];
    }

    private function handleBudgetStatus(int $userId, array $params): array
    {
        $status = $this->budgetService->getStatus($userId, $params['category'] ?? null);

        if (!$status['exists']) {
            return [
                'response' => $status['message'],
                'type' => 'budget',
            ];
        }

        $response = "📊 {$status['label']} Budget for {$status['period']}:\n";
        $response .= "Budget: RS {$status['budget']} | Spent: RS {$status['spent']} | Remaining: RS {$status['remaining']}\n";
        $response .= "Used: {$status['percentage']}% | Days left: {$status['days_left']}";

        if ($status['daily_budget'] > 0) {
            $response .= " | Daily budget: RS {$status['daily_budget']}";
        }

        if ($status['overspent'] > 0) {
            $response .= "\n⚠️ Overspent by RS {$status['overspent']}!";
        }

        return [
            'response' => $response,
            'type' => 'budget',
            'data' => $status,
        ];
    }

    private function handleSimulation(int $userId, array $params): array
    {
        $type = $params['type'] ?? 'generic';

        if ($type === 'generic') {
            return [
                'response' => "Try something like:\n- \"What if I reduce food by 20%?\"\n- \"Cancel Netflix\"\n- \"Increase budget to 60000\"",
                'type' => 'simulation',
            ];
        }

        $result = $this->budgetService->simulate($userId, $type, $params);

        return [
            'response' => $result['message'],
            'type' => 'simulation',
            'data' => $result,
        ];
    }

    private function handleAdvice(int $userId, string $originalMessage): array
    {
        $cacheKey = "advice_{$userId}_" . md5($originalMessage . now()->format('Y-m'));

        $cached = Cache::get($cacheKey);
        if ($cached) {
            return [
                'response' => $cached,
                'type' => 'advice',
                'cached' => true,
            ];
        }

        $data = $this->budgetService->getAdviceData($userId);

        $summary = [
            'budget' => $data['budget'],
            'spent' => $data['spent'],
            'remaining' => $data['remaining'],
            'projection' => $data['projection'],
            'previous_month' => $data['previous_month'],
            'change_vs_last_month' => $data['change_vs_last_month'],
            'avg_daily' => $data['avg_daily'],
            'top_category' => $data['top_category'],
            'top_category_amount' => $data['top_category_amount'],
            'top_category_budget' => $data['top_category_budget'],
        ];

        try {
            $advice = $this->callGroqForAdvice($summary);
            Cache::put($cacheKey, $advice, self::CACHE_TTL);

            return [
                'response' => $advice,
                'type' => 'advice',
                'cached' => false,
            ];
        } catch (\Throwable $e) {
            Log::warning('FinancialAdvisor: Groq unavailable, using fallback', ['error' => $e->getMessage()]);

            $fallback = $this->generateFallbackAdvice($data);

            return [
                'response' => $fallback . "\n\n💡 AI advice is temporarily unavailable. Your financial data is still available.",
                'type' => 'advice',
                'cached' => false,
                'fallback' => true,
            ];
        }
    }

    private function callGroqForAdvice(array $summary): string
    {
        $prompt = "You are a financial advisor assistant for a Pakistani user. All amounts are in RS (Pakistani Rupees). Use ONLY the numbers in the provided JSON data below. Never invent or calculate. Always use 'RS' as the currency symbol, never use '$'. Just explain the financial situation in 2-4 clear sentences.\n\nData: " . json_encode($summary);

        return $this->groqService->ask($prompt, 0.3, 512);
    }

    private function generateFallbackAdvice(array $data): string
    {
        $lines = [];

        if ($data['budget'] > 0 && $data['spent'] > $data['budget']) {
            $overspent = $data['spent'] - $data['budget'];
            $lines[] = "⚠️ You've exceeded your RS {$data['budget']} budget — you're RS {$overspent} over.";
        } elseif ($data['budget'] > 0) {
            $remaining = $data['budget'] - $data['spent'];
            $lines[] = "✅ You're within budget with RS {$remaining} remaining (RS {$data['budget']} total).";
        }

        if ($data['top_category'] !== 'N/A') {
            $lines[] = "Your top spending category is {$data['top_category']} (RS {$data['top_category_amount']}).";
        }

        if ($data['previous_month'] > 0) {
            $change = $data['change_vs_last_month'];
            $direction = $change > 0 ? 'up' : 'down';
            $lines[] = "Spending is {$direction} " . number_format(abs($change), 1) . "% vs last month (RS {$data['previous_month']}).";
        }

        $lines[] = "Your daily average is RS {$data['avg_daily']}, projecting to RS {$data['projection']} by month end.";

        return implode("\n", $lines);
    }

    private function handleUnknown(string $message): array
    {
        return [
            'response' => "I can help you with:\n\n" .
                "📋 **Budgets** — \"Set food budget to 5000\", \"How much is left?\"\n" .
                "📊 **Status** — \"Budget status\", \"What did I spend?\"\n" .
                "🔮 **Simulations** — \"What if I reduce food by 20%?\", \"Cancel Netflix\"\n" .
                "💡 **Advice** — \"How can I save money?\", \"Analyze my spending\"",
            'type' => 'help',
        ];
    }
}
