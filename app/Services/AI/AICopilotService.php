<?php

namespace App\Services\AI;

use App\Models\User;
use App\Models\Goal;
use App\Models\RecurringExpense;
use App\Services\BudgetService;
use App\Services\ExpenseService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AICopilotService
{
    public function __construct(
        private readonly GroqService $groqService,
        private readonly ExpenseService $expenseService,
        private readonly BudgetService $budgetService
    ) {}

    public function processUserMessage(User $user, string $message): array
    {
        // 1. Get user preferences
        $preferences = $user->preferences ?? [];

        // 2. Parse intents
        try {
            $intents = $this->groqService->parseCopilotIntents($message, $preferences);
        } catch (\Exception $e) {
            Log::error("Failed to parse intents: " . $e->getMessage());
            $intents = [['action' => 'ask_insight', 'question' => $message]];
        }

        // 3. Execute actions and collect context
        $context = [
            'expenses_added' => [],
            'budgets_updated' => [],
            'goals_updated' => [],
            'preferences_set' => [],
            'recurring_set' => [],
            'alerts' => [],
            'insights_requested' => false,
            'errors' => []
        ];

        foreach ($intents as $intent) {
            try {
                $action = $intent['action'] ?? 'unknown';
                
                if ($action === 'create_expense') {
                    $amount = $intent['amount'] ?? 0;
                    if ($amount > 0) {
                        $expense = $this->expenseService->create([
                            'user_id' => $user->id,
                            'amount' => $amount,
                            'category' => $intent['category'] ?? 'Other',
                            'merchant' => $intent['merchant'] ?? 'Unknown',
                            'description' => $intent['merchant'] ?? '',
                            'date' => $intent['date'] ?? now()->toDateString(),
                            'payment_method' => 'Cash',
                        ]);
                        
                        $context['expenses_added'][] = [
                            'amount' => $amount,
                            'category' => $expense->category,
                            'merchant' => $expense->merchant
                        ];

                        // Check budget alerts
                        $alerts = $this->budgetService->checkAlerts($user->id, $expense->category, $amount);
                        foreach ($alerts as $alert) {
                            $context['alerts'][] = $alert['message'];
                        }
                    }
                } elseif ($action === 'manage_budget') {
                    $type = $intent['type'] ?? 'set';
                    $category = $intent['category'] ?? null;
                    $amount = $intent['amount'] ?? 0;
                    $period = $intent['period'] ?? 'monthly';
                    
                    if ($type === 'remove' || $type === 'delete') {
                        // For simplicity, just set to 0. Real deletion would need repo update.
                        $result = $this->budgetService->setBudget($user->id, 0, $category, $period);
                        $context['budgets_updated'][] = "Removed {$period} budget for " . ($category ?? 'Overall');
                    } else {
                        if ($type === 'increase' || $type === 'add') {
                            $status = $this->budgetService->getStatus($user->id, $category);
                            $amount += ($status['budget'] ?? 0);
                        } elseif ($type === 'decrease') {
                            $status = $this->budgetService->getStatus($user->id, $category);
                            $amount = max(0, ($status['budget'] ?? 0) - $amount);
                        }
                        
                        $result = $this->budgetService->setBudget($user->id, $amount, $category, $period);
                        $context['budgets_updated'][] = "Set {$period} budget for " . ($category ?? 'Overall') . " to RS {$amount}";
                    }
                } elseif ($action === 'manage_goal') {
                    $type = $intent['type'] ?? 'create';
                    if ($type === 'create' || $type === 'update') {
                        $goal = Goal::updateOrCreate(
                            ['user_id' => $user->id, 'name' => $intent['name']],
                            ['target_amount' => $intent['target_amount'] ?? 0, 'deadline' => $intent['deadline'] ?? null]
                        );
                        $context['goals_updated'][] = "Goal '{$goal->name}' set to RS {$goal->target_amount}";
                    } elseif ($type === 'delete') {
                        Goal::where('user_id', $user->id)->where('name', $intent['name'])->delete();
                        $context['goals_updated'][] = "Goal '{$intent['name']}' deleted";
                    }
                } elseif ($action === 'set_preference') {
                    if (isset($intent['key']) && isset($intent['value'])) {
                        $preferences[$intent['key']] = $intent['value'];
                        $user->preferences = $preferences;
                        $user->save();
                        $context['preferences_set'][] = "Mapped {$intent['key']} to {$intent['value']}";
                    }
                } elseif ($action === 'make_recurring') {
                    $context['recurring_set'][] = "Set expense as recurring";
                } elseif ($action === 'ask_insight') {
                    $context['insights_requested'] = true;
                }
            } catch (\Exception $e) {
                Log::error("Action execution failed: " . $e->getMessage());
                $context['errors'][] = "Failed to execute action: " . ($intent['action'] ?? 'unknown');
            }
        }

        // 4. Gather Financial Context for AI Response
        $overallStatus = $this->budgetService->getStatus($user->id, null);
        $activeGoals = Goal::where('user_id', $user->id)->where('status', 'active')->get();
        $totalSavingsTarget = $activeGoals->sum('target_amount');
        
        $daysLeft = $overallStatus['days_left'] ?? 1;
        if ($daysLeft < 1) $daysLeft = 1;

        $spendableAmount = max(0, ($overallStatus['remaining'] ?? 0) - $totalSavingsTarget);
        $dailySpendingLimit = round($spendableAmount / $daysLeft, 2);

        $budgetAmount = $overallStatus['budget'] ?? 0;
        $statusLabel = 'On Track';
        
        // Calculate true remaining (can be negative if overspent)
        $trueRemaining = ($overallStatus['remaining'] ?? 0) - $totalSavingsTarget;
        
        if ($trueRemaining < 0) {
            $statusLabel = 'Over Budget';
        } elseif ($budgetAmount > 0 && $spendableAmount < ($budgetAmount * 0.20)) {
            $statusLabel = 'Warning (Low Budget)';
        }
        
        $categoryBreakdown = app(\App\Repositories\ExpenseRepositoryInterface::class)->getCategoryDistribution($user->id);

        $financialState = [
            'recent_actions' => $context,
            'overall_status' => $overallStatus,
            'category_breakdown' => $categoryBreakdown->toArray(),
            'active_goals' => $activeGoals->toArray(),
            'total_savings_target' => $totalSavingsTarget,
            'spendable_amount' => $spendableAmount,
            'daily_spending_limit' => $dailySpendingLimit,
            'calculated_status' => $statusLabel,
        ];

        // 5. Generate Conversational Response
        $systemPrompt = <<<PROMPT
You are Foresight, a smart, friendly, and human-like financial advisor for a Pakistani user (amounts in RS).
You are NOT a calculator. All calculations have already been performed by the backend.

Action Results & Backend Data Context (JSON):
{context}

Original User Message: "{message}"

CRITICAL RESPONSE RULES:
1. Understand the user's intent and read the calculated data from the JSON above (including overall_status and category_breakdown).
2. DO NOT perform calculations. DO NOT invent numbers. Use ONLY the values provided by the backend.
3. Generate a natural, human-like response. If they ask about a specific category, look it up in category_breakdown.
4. EXTREMELY IMPORTANT: Keep responses extremely short (1-2 lines maximum) for simple questions like checking balances or budgets. DO NOT add unnecessary filler.
5. If the user asks a complex question, provide a slightly more detailed explanation (3-5 lines maximum).
6. Give one very short practical suggestion or piece of financial advice.
7. DO NOT use rigid bulleted lists or dashboard-like formats. Write it as a natural, conversational paragraph.
PROMPT;

        $systemPrompt = str_replace(
            ['{context}', '{message}'],
            [json_encode($financialState), $message],
            $systemPrompt
        );

        $response = $this->groqService->getAdvisorResponse([
            ['role' => 'system', 'content' => $systemPrompt]
        ], 0.3);

        $hasUpdates = count($context['expenses_added']) > 0 
                   || count($context['budgets_updated']) > 0 
                   || count($context['goals_updated']) > 0;

        return [
            'response' => $response,
            'should_refresh' => $hasUpdates
        ];
    }
}
