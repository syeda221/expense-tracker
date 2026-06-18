<?php

namespace App\Repositories;

use App\Models\Expense;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ExpenseRepositoryInterface
{
    public function all(int $userId): Collection;

    public function findByUserPaginated(int $userId, array $filters = []): LengthAwarePaginator;

    public function find(int $id): ?Expense;

    public function create(array $data): Expense;

    public function update(Expense $expense, array $data): Expense;

    public function delete(Expense $expense): bool;

    public function getTodayTotal(int $userId): float;

    public function getMonthlyTotal(int $userId): float;

    public function getYearlyTotal(int $userId): float;

    public function getHighestExpense(int $userId): ?Expense;

    public function getTopCategory(int $userId): ?string;

    public function getRecentExpenses(int $userId, int $limit = 5): Collection;

    public function getMonthlySpending(int $userId): Collection;

    public function getCategoryDistribution(int $userId): Collection;

    public function getTodayCount(int $userId): int;

    public function getMonthlyCount(int $userId): int;

    public function getYearlyCount(int $userId): int;

    public function getPreviousMonthTotal(int $userId): float;

    public function getRecurringCount(int $userId): int;

    public function getExpenseCount(int $userId): int;

    public function getWeeklyTrend(int $userId): Collection;

    public function getCurrentMonthDailyBreakdown(int $userId): Collection;
}
