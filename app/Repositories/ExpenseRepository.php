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

    public function findByUserPaginated(int $userId, array $filters = []): LengthAwarePaginator
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

        if (!empty($filters['date_from'])) {
            $query->whereDate('expense_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('expense_date', '<=', $filters['date_to']);
        }

        return $query->orderBy('expense_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();
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
}
