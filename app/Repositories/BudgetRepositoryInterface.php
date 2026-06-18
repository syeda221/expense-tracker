<?php

namespace App\Repositories;

use App\Models\Budget;
use App\Models\BudgetAlertSent;
use Illuminate\Support\Collection;

interface BudgetRepositoryInterface
{
    public function getUserBudgets(int $userId): Collection;

    public function findForUser(int $id, int $userId): ?Budget;

    public function findByCategory(int $userId, ?string $category, string $periodType, string $periodStart): ?Budget;

    public function create(array $data): Budget;

    public function update(Budget $budget, array $data): Budget;

    public function delete(Budget $budget): bool;

    public function alertExists(int $budgetId, int $userId, float $threshold, string $periodStart): bool;

    public function createAlert(array $data): BudgetAlertSent;
}
