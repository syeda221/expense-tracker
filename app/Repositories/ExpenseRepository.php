<?php

namespace App\Repositories;

use App\Models\Expense;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ExpenseRepository implements ExpenseRepositoryInterface
{
    public function all(int $userId): Collection
    {
        return Expense::where('user_id', $userId)->get();
    }

    private function buildFilteredQuery(int $userId, array $filters = []): \Illuminate\Database\Eloquent\Builder
    {
        $query = Expense::where('user_id', $userId);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('merchant', 'like', "%{$search}%")
                  ->orWhere('category', $search)
                  ->orWhere('payment_method', $search);
            });
        }

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['merchant'])) {
            $query->where('merchant', 'like', "%{$filters['merchant']}%");
        }

        if (!empty($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        $dateFrom = $filters['date_from'] ?? $filters['start_date'] ?? null;
        if (!empty($dateFrom)) {
            $query->whereDate('expense_date', '>=', $dateFrom);
        }

        $dateTo = $filters['date_to'] ?? $filters['end_date'] ?? null;
        if (!empty($dateTo)) {
            $query->whereDate('expense_date', '<=', $dateTo);
        }

        if (isset($filters['min_amount']) && is_numeric($filters['min_amount'])) {
            $query->where('amount', '>=', (float) $filters['min_amount']);
        }

        if (isset($filters['max_amount']) && is_numeric($filters['max_amount'])) {
            $query->where('amount', '<=', (float) $filters['max_amount']);
        }

        if (isset($filters['is_recurring'])) {
            $query->where('is_recurring', filter_var($filters['is_recurring'], FILTER_VALIDATE_BOOLEAN));
        }

        return $query;
    }

    public function findByUserPaginated(int $userId, array $filters = []): LengthAwarePaginator
    {
        $query = $this->buildFilteredQuery($userId, $filters);

        $sort = $filters['sort'] ?? 'expense_date';
        $order = $filters['order'] ?? 'desc';

        return $query->orderBy($sort, $order)
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();
    }

    public function sumByFilters(int $userId, array $filters = []): array
    {
        $query = $this->buildFilteredQuery($userId, $filters);

        return [
            'total' => (float) $query->sum('amount'),
            'count' => $query->count(),
        ];
    }

    public function find(int $id): ?Expense
    {
        return Expense::find($id);
    }

    public function create(array $data): Expense
    {
        return Expense::create($data);
    }

    public function update(Expense $expense, array $data): Expense
    {
        $expense->update($data);
        return $expense->fresh();
    }

    public function delete(Expense $expense): bool
    {
        return $expense->delete();
    }

    public function getTodayTotal(int $userId): float
    {
        return (float) Expense::where('user_id', $userId)
            ->whereDate('expense_date', today())
            ->sum('amount');
    }

    public function getMonthlyTotal(int $userId): float
    {
        return (float) Expense::where('user_id', $userId)
            ->whereYear('expense_date', now()->year)
            ->whereMonth('expense_date', now()->month)
            ->sum('amount');
    }

    public function getYearlyTotal(int $userId): float
    {
        return (float) Expense::where('user_id', $userId)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');
    }

    public function getHighestExpense(int $userId): ?Expense
    {
        return Expense::where('user_id', $userId)
            ->orderBy('amount', 'desc')
            ->first();
    }

    public function getTopCategory(int $userId): ?string
    {
        return Expense::where('user_id', $userId)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->value('category');
    }

    public function getRecentExpenses(int $userId, int $limit = 5): Collection
    {
        return Expense::where('user_id', $userId)
            ->orderBy('expense_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getMonthlySpending(int $userId): Collection
    {
        return Expense::where('user_id', $userId)
            ->whereYear('expense_date', now()->year)
            ->selectRaw('MONTH(expense_date) as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    public function getCategoryDistribution(int $userId): Collection
    {
        return Expense::where('user_id', $userId)
            ->selectRaw('category, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->get();
    }

    public function getTodayCount(int $userId): int
    {
        return Expense::where('user_id', $userId)
            ->whereDate('expense_date', today())
            ->count();
    }

    public function getMonthlyCount(int $userId): int
    {
        return Expense::where('user_id', $userId)
            ->whereYear('expense_date', now()->year)
            ->whereMonth('expense_date', now()->month)
            ->count();
    }

    public function getYearlyCount(int $userId): int
    {
        return Expense::where('user_id', $userId)
            ->whereYear('expense_date', now()->year)
            ->count();
    }

    public function getPreviousMonthTotal(int $userId): float
    {
        $prev = now()->subMonth();
        return (float) Expense::where('user_id', $userId)
            ->whereYear('expense_date', $prev->year)
            ->whereMonth('expense_date', $prev->month)
            ->sum('amount');
    }

    public function getRecurringCount(int $userId): int
    {
        return Expense::where('user_id', $userId)
            ->where('is_recurring', true)
            ->count();
    }

    public function getExpenseCount(int $userId): int
    {
        return Expense::where('user_id', $userId)->count();
    }

    public function getWeeklyTrend(int $userId): Collection
    {
        return Expense::where('user_id', $userId)
            ->whereYear('expense_date', now()->year)
            ->whereMonth('expense_date', now()->month)
            ->selectRaw('WEEK(expense_date, 1) - WEEK(DATE_SUB(expense_date, INTERVAL DAYOFMONTH(expense_date)-1 DAY), 1) + 1 as week, SUM(amount) as total')
            ->groupBy('week')
            ->orderBy('week')
            ->get();
    }

    public function getCurrentMonthDailyBreakdown(int $userId): Collection
    {
        return Expense::where('user_id', $userId)
            ->whereYear('expense_date', now()->year)
            ->whereMonth('expense_date', now()->month)
            ->selectRaw('DATE(expense_date) as date, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public function getDailyBreakdown(int $userId, string $startDate, string $endDate): Collection
    {
        return Expense::where('user_id', $userId)
            ->whereDate('expense_date', '>=', $startDate)
            ->whereDate('expense_date', '<=', $endDate)
            ->selectRaw('DATE(expense_date) as date, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public function getPeriodTotal(int $userId, string $startDate, string $endDate): float
    {
        return (float) Expense::where('user_id', $userId)
            ->whereDate('expense_date', '>=', $startDate)
            ->whereDate('expense_date', '<=', $endDate)
            ->sum('amount');
    }

    public function getPeriodCount(int $userId, string $startDate, string $endDate): int
    {
        return Expense::where('user_id', $userId)
            ->whereDate('expense_date', '>=', $startDate)
            ->whereDate('expense_date', '<=', $endDate)
            ->count();
    }

    public function getCategoryTotalForPeriod(int $userId, string $category, string $startDate, string $endDate): float
    {
        return (float) Expense::where('user_id', $userId)
            ->where('category', $category)
            ->whereDate('expense_date', '>=', $startDate)
            ->whereDate('expense_date', '<=', $endDate)
            ->sum('amount');
    }

    public function getMerchantTotalForPeriod(int $userId, string $merchant, string $startDate, string $endDate): float
    {
        return (float) Expense::where('user_id', $userId)
            ->where('merchant', $merchant)
            ->whereDate('expense_date', '>=', $startDate)
            ->whereDate('expense_date', '<=', $endDate)
            ->sum('amount');
    }
}
