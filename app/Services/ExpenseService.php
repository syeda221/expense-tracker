<?php

namespace App\Services;

use App\Models\Expense;
use App\Repositories\ExpenseRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ExpenseService
{
    public function __construct(
        private readonly ExpenseRepositoryInterface $expenseRepository
    ) {}

    public function getPaginated(int $userId, array $filters = []): LengthAwarePaginator
    {
        return $this->expenseRepository->findByUserPaginated($userId, $filters);
    }

    public function find(int $id): ?Expense
    {
        return $this->expenseRepository->find($id);
    }

    public function create(array $data): Expense
    {
        return $this->expenseRepository->create($data);
    }

    public function update(Expense $expense, array $data): Expense
    {
        return $this->expenseRepository->update($expense, $data);
    }

    public function delete(Expense $expense): bool
    {
        return $this->expenseRepository->delete($expense);
    }

    public function getTodayTotal(int $userId): float
    {
        return $this->expenseRepository->getTodayTotal($userId);
    }

    public function getMonthlyTotal(int $userId): float
    {
        return $this->expenseRepository->getMonthlyTotal($userId);
    }

    public function getYearlyTotal(int $userId): float
    {
        return $this->expenseRepository->getYearlyTotal($userId);
    }

    public function getHighestExpense(int $userId): ?Expense
    {
        return $this->expenseRepository->getHighestExpense($userId);
    }

    public function getTopCategory(int $userId): ?string
    {
        return $this->expenseRepository->getTopCategory($userId);
    }

    public function getRecentExpenses(int $userId, int $limit = 5): Collection
    {
        return $this->expenseRepository->getRecentExpenses($userId, $limit);
    }

    public function getDashboardStats(int $userId): array
    {
        return [
            'todayTotal' => $this->getTodayTotal($userId),
            'todayCount' => $this->expenseRepository->getTodayCount($userId),
            'monthlyTotal' => $this->getMonthlyTotal($userId),
            'monthlyCount' => $this->expenseRepository->getMonthlyCount($userId),
            'yearlyTotal' => $this->getYearlyTotal($userId),
            'yearlyCount' => $this->expenseRepository->getYearlyCount($userId),
            'highestExpense' => $this->getHighestExpense($userId),
            'topCategory' => $this->getTopCategory($userId),
            'recentExpenses' => $this->getRecentExpenses($userId),
            'monthlySpending' => $this->expenseRepository->getMonthlySpending($userId),
            'categoryDistribution' => $this->expenseRepository->getCategoryDistribution($userId),
            'dailyBreakdown' => $this->expenseRepository->getCurrentMonthDailyBreakdown($userId),
        ];
    }
}
