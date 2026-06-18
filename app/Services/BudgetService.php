<?php

namespace App\Services;

use App\Repositories\BudgetRepositoryInterface;
use App\Repositories\ExpenseRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BudgetService
{
    public function __construct(
        private readonly BudgetRepositoryInterface $budgetRepo,
        private readonly ExpenseRepositoryInterface $expenseRepo,
    ) {}

    public function setBudget(int $userId, float $amount, ?string $category = null, string $periodType = 'monthly', ?string $periodStart = null): array
    {
        $periodStart ??= now()->startOfMonth()->toDateString();

        $existing = $this->budgetRepo->findByCategory($userId, $category, $periodType, $periodStart);

        if ($existing) {
            $this->budgetRepo->update($existing, ['amount' => $amount]);
            $action = 'updated';
        } else {
            $this->budgetRepo->create([
                'user_id' => $userId,
                'category' => $category,
                'amount' => $amount,
                'period_type' => $periodType,
                'period_start' => $periodStart,
            ]);
            $action = 'set';
        }

        $label = $category ?? 'Overall';

        return [
            'action' => $action,
            'label' => $label,
            'amount' => $amount,
            'period_type' => $periodType,
            'message' => "Your {$periodType} {$label} budget has been {$action} to $" . number_format($amount, 2) . ".",
        ];
    }

    public function getStatus(int $userId, ?string $category = null): array
    {
        $periodStart = now()->startOfMonth()->toDateString();
        $periodType = 'monthly';
        $periodEnd = now()->endOfMonth()->toDateString();

        $budget = $this->budgetRepo->findByCategory($userId, $category, $periodType, $periodStart);

        if (!$budget) {
            $label = $category ?? 'Overall';
            return [
                'exists' => false,
                'message' => "No {$label} budget set for this month.",
            ];
        }

        $spent = $category
            ? $this->expenseRepo->getCategoryTotalForPeriod($userId, $category, $periodStart, $periodEnd)
            : $this->expenseRepo->getMonthlyTotal($userId);

        $remaining = $budget->amount - $spent;
        $percentage = $budget->amount > 0 ? round(($spent / $budget->amount) * 100, 1) : 0;
        $daysInMonth = now()->daysInMonth;
        $daysLeft = $daysInMonth - now()->day;
        $dailyBudget = $daysLeft > 0 ? round($remaining / $daysLeft, 2) : 0;

        $label = $category ?? 'Overall';

        return [
            'exists' => true,
            'label' => $label,
            'budget' => (float) $budget->amount,
            'spent' => $spent,
            'remaining' => max(0, $remaining),
            'overspent' => $remaining < 0 ? abs($remaining) : 0,
            'percentage' => $percentage,
            'days_left' => $daysLeft,
            'daily_budget' => $dailyBudget,
            'days_in_month' => $daysInMonth,
            'period' => now()->format('F Y'),
        ];
    }

    public function getAllBudgetsStatus(int $userId): array
    {
        $budgets = $this->budgetRepo->getUserBudgets($userId);
        $results = [];

        foreach ($budgets as $budget) {
            $status = $this->getStatus($userId, $budget->category);
            $results[] = $status;
        }

        return $results;
    }

    public function getDashboardSummary(int $userId): array
    {
        $overallStatus = $this->getStatus($userId, null);
        $categoryBudgets = $this->getAllBudgetsStatus($userId);
        $alerts = $this->getActiveAlerts($userId);

        $hasBudget = $overallStatus['exists'] ?? false;

        return [
            'has_budget' => $hasBudget,
            'overall' => $overallStatus,
            'category_budgets' => $categoryBudgets,
            'alerts' => $alerts,
        ];
    }

    public function checkAlerts(int $userId, string $category, float $amount): array
    {
        $periodStart = now()->startOfMonth()->toDateString();
        $triggers = [];

        $budgetsToCheck = [$this->budgetRepo->findByCategory($userId, null, 'monthly', $periodStart)];

        if ($category) {
            $budgetsToCheck[] = $this->budgetRepo->findByCategory($userId, $category, 'monthly', $periodStart);
        }

        foreach (array_filter($budgetsToCheck) as $budget) {
            $status = $this->getStatus($userId, $budget->category);
            $thresholds = [80, 100, 110];

            foreach ($thresholds as $threshold) {
                if ($status['percentage'] >= $threshold) {
                    $alreadySent = $this->budgetRepo->alertExists(
                        $budget->id, $userId, $threshold, $periodStart
                    );

                    if (!$alreadySent) {
                        $this->budgetRepo->createAlert([
                            'user_id' => $userId,
                            'budget_id' => $budget->id,
                            'threshold_percent' => $threshold,
                            'period_start' => $periodStart,
                            'sent_at' => now(),
                        ]);

                        $label = $budget->category ?? 'Overall';
                        $triggers[] = [
                            'label' => $label,
                            'threshold' => $threshold,
                            'spent' => $status['spent'],
                            'budget' => $status['budget'],
                            'message' => "⚠️ {$label} budget is at {$status['percentage']}% (\${$status['spent']} of \${$status['budget']}).",
                        ];
                    }
                }
            }
        }

        return $triggers;
    }

    public function getActiveAlerts(int $userId): array
    {
        $periodStart = now()->startOfMonth()->toDateString();
        $alerts = [];

        $budgets = $this->budgetRepo->getUserBudgets($userId);

        foreach ($budgets as $budget) {
            $status = $this->getStatus($userId, $budget->category);
            if ($status['exists'] && $status['percentage'] >= 80) {
                $label = $budget->category ?? 'Overall';
                $alerts[] = [
                    'label' => $label,
                    'percentage' => $status['percentage'],
                    'spent' => $status['spent'],
                    'budget' => $status['budget'],
                ];
            }
        }

        return $alerts;
    }

    public function simulate(int $userId, string $type, array $params): array
    {
        $periodStart = now()->startOfMonth()->toDateString();
        $periodEnd = now()->endOfMonth()->toDateString();
        $monthlyTotal = $this->expenseRepo->getMonthlyTotal($userId);
        $overallBudget = $this->budgetRepo->findByCategory($userId, null, 'monthly', $periodStart);

        $result = [
            'current_spending' => $monthlyTotal,
            'current_budget' => $overallBudget?->amount ?? 0,
        ];

        if ($type === 'reduce_category') {
            $category = $params['category'] ?? 'Food & Dining';
            $percent = (float) ($params['percent'] ?? 0);
            $currentCatSpending = $this->expenseRepo->getCategoryTotalForPeriod($userId, $category, $periodStart, $periodEnd);
            $saving = round($currentCatSpending * ($percent / 100), 2);
            $projected = max(0, $monthlyTotal - $saving);

            $result['category'] = $category;
            $result['category_current'] = $currentCatSpending;
            $result['reduction_percent'] = $percent;
            $result['savings'] = $saving;
            $result['projected_total'] = $projected;
            $result['message'] = "Reducing {$category} by {$percent}% (currently \${$currentCatSpending}) would save \${$saving} this month, bringing your total to \${$projected}.";

        } elseif ($type === 'cancel_subscription') {
            $merchant = $params['merchant'] ?? '';
            $merchantTotal = $this->expenseRepo->getMerchantTotalForPeriod($userId, $merchant, $periodStart, $periodEnd);
            $saving = $merchantTotal;
            $projected = max(0, $monthlyTotal - $saving);

            $result['merchant'] = $merchant;
            $result['merchant_total'] = $merchantTotal;
            $result['savings'] = $saving;
            $result['projected_total'] = $projected;
            $result['message'] = "Cancelling {$merchant} (currently \${$merchantTotal} this month) would save \${$saving}, bringing your total to \${$projected}.";

        } elseif ($type === 'increase_budget') {
            $newBudget = (float) ($params['amount'] ?? $monthlyTotal);
            $result['new_budget'] = $newBudget;
            $result['message'] = "Increasing budget to \${$newBudget} gives you \$" . max(0, $newBudget - $monthlyTotal) . " in remaining budget.";
        }

        return $result;
    }

    public function getAdviceData(int $userId): array
    {
        $periodStart = now()->startOfMonth()->toDateString();
        $periodEnd = now()->endOfMonth()->toDateString();
        $prevPeriodStart = now()->subMonth()->startOfMonth()->toDateString();
        $prevPeriodEnd = now()->subMonth()->endOfMonth()->toDateString();

        $currentMonth = $this->expenseRepo->getMonthlyTotal($userId);
        $previousMonth = $this->expenseRepo->getPreviousMonthTotal($userId);
        $categories = $this->expenseRepo->getCategoryDistribution($userId);
        $overallBudget = $this->budgetRepo->findByCategory($userId, null, 'monthly', $periodStart);
        $budgetAmount = $overallBudget?->amount ?? 0;
        $remaining = max(0, $budgetAmount - $currentMonth);
        $dailyBreakdown = $this->expenseRepo->getCurrentMonthDailyBreakdown($userId);
        $daysIn = now()->day;
        $avgDaily = $daysIn > 0 ? round($currentMonth / $daysIn, 2) : 0;
        $projected = $avgDaily * now()->daysInMonth;

        $topCat = $categories->first();
        $topCategoryName = $topCat?->category ?? 'N/A';
        $topCategoryAmount = (float) ($topCat?->total ?? 0);

        $topCatBudget = null;
        if ($topCategoryName !== 'N/A') {
            $topCatBudgetModel = $this->budgetRepo->findByCategory($userId, $topCategoryName, 'monthly', $periodStart);
            $topCatBudget = $topCatBudgetModel?->amount ? (float) $topCatBudgetModel->amount : null;
        }

        return [
            'budget' => $budgetAmount,
            'spent' => $currentMonth,
            'remaining' => $remaining,
            'projection' => round($projected, 2),
            'previous_month' => $previousMonth,
            'change_vs_last_month' => $previousMonth > 0 ? round(($currentMonth - $previousMonth) / $previousMonth * 100, 1) : 0,
            'avg_daily' => $avgDaily,
            'top_category' => $topCategoryName,
            'top_category_amount' => $topCategoryAmount,
            'top_category_budget' => $topCatBudget,
            'days_in_month' => now()->daysInMonth,
            'days_elapsed' => $daysIn,
        ];
    }
}
