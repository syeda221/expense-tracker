<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Budget;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SpendingAnalyticsService
{
    /**
     * Get summary of spending for the current week.
     */
    public function getWeeklySummary(int $userId): array
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        $lastWeekStart = Carbon::now()->subWeek()->startOfWeek();
        $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek();

        $thisWeekSpent = Expense::where('user_id', $userId)
            ->whereBetween('expense_date', [$startOfWeek, $endOfWeek])
            ->sum('amount');

        $lastWeekSpent = Expense::where('user_id', $userId)
            ->whereBetween('expense_date', [$lastWeekStart, $lastWeekEnd])
            ->sum('amount');

        $topCategory = Expense::where('user_id', $userId)
            ->whereBetween('expense_date', [$startOfWeek, $endOfWeek])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->first();

        $daysElapsed = Carbon::now()->dayOfWeek === 0 ? 7 : Carbon::now()->dayOfWeek;

        return [
            'total_spent_this_week' => round((float)$thisWeekSpent, 2),
            'comparison_with_last_week' => round((float)($thisWeekSpent - $lastWeekSpent), 2),
            'top_category' => $topCategory ? $topCategory->category : 'None',
            'top_category_amount' => $topCategory ? round((float)$topCategory->total, 2) : 0,
            'average_daily_spending' => round((float)($thisWeekSpent / $daysElapsed), 2),
            'transaction_count' => Expense::where('user_id', $userId)->whereBetween('expense_date', [$startOfWeek, $endOfWeek])->count(),
        ];
    }

    /**
     * Get monthly progress and projections.
     */
    public function getMonthlyProgress(int $userId): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $daysInMonth = $startOfMonth->daysInMonth;
        $daysElapsed = Carbon::now()->day;

        $spentThisMonth = Expense::where('user_id', $userId)
            ->whereBetween('expense_date', [$startOfMonth, Carbon::now()])
            ->sum('amount');

        $averageDaily = $daysElapsed > 0 ? $spentThisMonth / $daysElapsed : 0;
        $projectedTotal = $averageDaily * $daysInMonth;

        return [
            'current_month_spending' => round((float)$spentThisMonth, 2),
            'days_elapsed' => $daysElapsed,
            'days_remaining' => $daysInMonth - $daysElapsed,
            'average_daily_spending' => round((float)$averageDaily, 2),
            'projected_month_end_spending' => round((float)$projectedTotal, 2),
        ];
    }

    /**
     * Check for budget alerts based on a 3-month rolling average or existing budgets.
     */
    public function checkBudgetAlerts(int $userId): array
    {
        $alerts = [];
        
        $startOfMonth = Carbon::now()->startOfMonth();
        $threeMonthsAgo = Carbon::now()->subMonths(3)->startOfMonth();

        $currentSpending = Expense::where('user_id', $userId)
            ->whereBetween('expense_date', [$startOfMonth, Carbon::now()])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get()
            ->keyBy('category');

        $rollingAverages = Expense::where('user_id', $userId)
            ->whereBetween('expense_date', [$threeMonthsAgo, $startOfMonth->subDay()])
            ->select('category', DB::raw('SUM(amount) / 3 as avg_total'))
            ->groupBy('category')
            ->get()
            ->keyBy('category');

        foreach ($currentSpending as $category => $data) {
            $current = $data->total;
            $avg = $rollingAverages->has($category) ? $rollingAverages[$category]->avg_total : 0;

            if ($avg > 0) {
                $percentUsed = ($current / $avg) * 100;
                if ($percentUsed >= 80) {
                    $alerts[] = [
                        'category' => $category,
                        'current_spending' => round((float)$current, 2),
                        'rolling_average' => round((float)$avg, 2),
                        'percent_used' => round((float)$percentUsed, 2),
                        'alert_type' => $percentUsed >= 100 ? 'exceeded' : 'nearly_exceeded'
                    ];
                }
            }
        }

        return $alerts;
    }

    /**
     * Get a savings suggestion if spending in a category is reduced by 50%.
     */
    public function getSavingsSuggestion(int $userId, string $category): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        
        $spent = Expense::where('user_id', $userId)
            ->where('category', $category)
            ->whereBetween('expense_date', [$startOfMonth, Carbon::now()])
            ->sum('amount');

        $monthlySavings = $spent * 0.5;
        
        return [
            'category' => $category,
            'current_spent' => round((float)$spent, 2),
            'potential_monthly_savings' => round((float)$monthlySavings, 2),
            'potential_yearly_savings' => round((float)($monthlySavings * 12), 2),
        ];
    }
}
