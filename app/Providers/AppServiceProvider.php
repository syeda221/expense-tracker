<?php

namespace App\Providers;

use App\Repositories\BudgetRepository;
use App\Repositories\BudgetRepositoryInterface;
use App\Repositories\ExpenseRepository;
use App\Repositories\ExpenseRepositoryInterface;
use App\Services\AI\AIServiceInterface;
use App\Services\AI\GeminiService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ExpenseRepositoryInterface::class, ExpenseRepository::class);
        $this->app->bind(BudgetRepositoryInterface::class, BudgetRepository::class);
        $this->app->bind(AIServiceInterface::class, GeminiService::class);
    }

    public function boot(): void
    {
        //
    }
}
