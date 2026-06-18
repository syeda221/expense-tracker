<?php

namespace App\Services\FinancialAdvisor;

class IntentDetector
{
    public function detect(string $message): array
    {
        $message = trim($message);
        $lower = mb_strtolower($message);

        // Budget set — e.g. "set food budget to 5000", "my budget is 30000"
        if (preg_match('/\b(?:set|create|new|change|update|add)\b.*\bbudget/', $lower) ||
            preg_match('/^my\s+budget/', $lower) ||
            preg_match('/^budget\s+(?:is|of|:)/', $lower)) {
            return $this->parseBudgetSet($message);
        }

        // Budget status — e.g. "how much left", "remaining budget", "budget status", "what's my budget"
        if (preg_match('/\b(?:left|remaining|balance|status|how much|spent)\b.*\bbudget/', $lower) ||
            preg_match('/\bbudget\b.*\b(?:left|remaining|balance|status)\b/', $lower) ||
            preg_match('/^(?:how\s+much\s+(?:did\s+I\s+)?spend|what\'?s?\s+(?:my\s+)?(?:remaining|budget|left))/', $lower) ||
            preg_match('/^budget\s+(?:status|summary)/', $lower)) {
            return [
                'intent' => 'budget_status',
                'confidence' => 0.9,
                'params' => $this->extractCategory($message),
            ];
        }

        // Simulation — e.g. "what if I reduce food by 20%", "cancel netflix", "suppose I increase budget"
        if (preg_match('/\b(?:what\s+if|suppose|imagine|if\s+I\s+reduce|if\s+I\s+increase|cancel|stop)\b/', $lower)) {
            return $this->parseSimulation($message, $lower);
        }

        // Advice — e.g. "how can I save", "financial advice", "analyze", "overspending", "recommend"
        if (preg_match('/\b(?:save\s+money|financial\s+advice|overspend|analyze|recommend|suggestion|improve|advice|how\s+can\s+I\s+save|spending\s+habits|what\s+should\s+I\s+do)\b/', $lower)) {
            return [
                'intent' => 'advice',
                'confidence' => 0.85,
                'params' => [],
            ];
        }

        // Generic budget query — e.g. "food budget", "show budget"
        if (preg_match('/\bbudget\b/', $lower)) {
            return [
                'intent' => 'budget_status',
                'confidence' => 0.7,
                'params' => $this->extractCategory($message),
            ];
        }

        return [
            'intent' => 'unknown',
            'confidence' => 0,
            'params' => [],
        ];
    }

    private function parseBudgetSet(string $message): array
    {
        $lower = mb_strtolower($message);
        $amount = null;
        $category = null;

        if (preg_match('/(\d[\d,]*)\s*(?:rs|pkr|usd|\$|usd)?/i', $message, $m)) {
            $amount = (float) str_replace(',', '', $m[1]);
        }

        $categoryKeywords = [
            'food', 'dining', 'groceries', 'transport', 'fuel', 'bills', 'utilities',
            'healthcare', 'education', 'entertainment', 'shopping', 'travel', 'rent',
            'subscription', 'investment',
        ];

        foreach ($categoryKeywords as $cat) {
            if (str_contains($lower, $cat)) {
                $category = ucfirst($cat === 'dining' ? 'Food & Dining' : ($cat === 'healthcare' ? 'Healthcare' : $cat));
                break;
            }
        }

        if ($category === null && str_contains($lower, 'overall')) {
            $category = null;
        }

        // Map aliases
        if (str_contains($lower, 'grocery')) $category = 'Groceries';
        if (str_contains($lower, 'fuel') || str_contains($lower, 'petrol')) $category = 'Fuel';
        if (str_contains($lower, 'bill') || str_contains($lower, 'utility')) $category = 'Bills';
        if (str_contains($lower, 'subscription') || str_contains($lower, 'netflix') || str_contains($lower, 'spotify')) $category = 'Subscription';
        if (str_contains($lower, 'rent')) $category = 'Rent';
        if (str_contains($lower, 'travel') || str_contains($lower, 'trip') || str_contains($lower, 'flight')) $category = 'Travel';

        $periodType = 'monthly';
        if (str_contains($lower, 'week')) $periodType = 'weekly';
        if (str_contains($lower, 'year')) $periodType = 'yearly';

        return [
            'intent' => 'budget_set',
            'confidence' => $amount ? 0.95 : 0.6,
            'params' => [
                'amount' => $amount,
                'category' => $category,
                'period_type' => $periodType,
            ],
        ];
    }

    private function parseSimulation(string $message, string $lower): array
    {
        $params = ['category' => null, 'percent' => null, 'merchant' => null, 'amount' => null];

        // "reduce X by Y%"
        if (preg_match('/reduce\s+(\w+(?:\s+\w+)?)\s+by\s+(\d+)/i', $message, $m)) {
            $params['category'] = ucfirst($m[1]);
            $params['percent'] = (int) $m[2];
            return ['intent' => 'simulation', 'confidence' => 0.95, 'params' => ['type' => 'reduce_category'] + $params];
        }

        // "cancel X" / "stop X"
        if (preg_match('/\b(?:cancel|stop)\s+(\w+(?:\s+\w+)?)/i', $message, $m)) {
            $params['merchant'] = ucfirst($m[1]);
            return ['intent' => 'simulation', 'confidence' => 0.9, 'params' => ['type' => 'cancel_subscription'] + $params];
        }

        // "increase budget to X" / "increase by X"
        if (preg_match('/increase\s+(?:budget\s+)?(?:to|by)\s+(\d+)/i', $message, $m)) {
            $params['amount'] = (float) str_replace(',', '', $m[1]);
            return ['intent' => 'simulation', 'confidence' => 0.9, 'params' => ['type' => 'increase_budget'] + $params];
        }

        // generic what-if
        return ['intent' => 'simulation', 'confidence' => 0.5, 'params' => ['type' => 'generic']];
    }

    private function extractCategory(string $message): array
    {
        $lower = mb_strtolower($message);
        $categories = [
            'food' => 'Food & Dining', 'dining' => 'Food & Dining', 'grocery' => 'Groceries',
            'transport' => 'Transport', 'fuel' => 'Fuel', 'petrol' => 'Fuel',
            'bill' => 'Bills', 'utility' => 'Bills', 'electricity' => 'Bills',
            'health' => 'Healthcare', 'medical' => 'Healthcare',
            'education' => 'Education', 'school' => 'Education', 'tuition' => 'Education',
            'entertainment' => 'Entertainment', 'shopping' => 'Shopping',
            'travel' => 'Travel', 'rent' => 'Rent', 'subscription' => 'Subscription',
            'netflix' => 'Subscription', 'investment' => 'Investment',
        ];

        foreach ($categories as $keyword => $cat) {
            if (str_contains($lower, $keyword)) {
                return ['category' => $cat];
            }
        }

        return ['category' => null];
    }
}
