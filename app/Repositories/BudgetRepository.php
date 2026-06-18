<?php

namespace App\Repositories;

use App\Models\Budget;
use App\Models\BudgetAlertSent;
use Illuminate\Support\Collection;

class BudgetRepository implements BudgetRepositoryInterface
{
    public function getUserBudgets(int $userId): Collection
    {
        return Budget::where('user_id', $userId)
            ->orderBy('category')
            ->get();
    }

    public function findForUser(int $id, int $userId): ?Budget
    {
        return Budget::where('id', $id)->where('user_id', $userId)->first();
    }

    public function findByCategory(int $userId, ?string $category, string $periodType, string $periodStart): ?Budget
    {
        $q = Budget::where('user_id', $userId)
            ->where('period_type', $periodType)
            ->where('period_start', $periodStart);

        if ($category) {
            $q->where('category', $category);
        } else {
            $q->whereNull('category');
        }

        return $q->first();
    }

    public function create(array $data): Budget
    {
        return Budget::create($data);
    }

    public function update(Budget $budget, array $data): Budget
    {
        $budget->update($data);
        return $budget->fresh();
    }

    public function delete(Budget $budget): bool
    {
        return $budget->delete();
    }

    public function alertExists(int $budgetId, int $userId, float $threshold, string $periodStart): bool
    {
        return BudgetAlertSent::where('budget_id', $budgetId)
            ->where('user_id', $userId)
            ->where('threshold_percent', $threshold)
            ->where('period_start', $periodStart)
            ->exists();
    }

    public function createAlert(array $data): BudgetAlertSent
    {
        return BudgetAlertSent::create($data);
    }
}
