<?php

use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Repositories\ExpenseRepositoryInterface;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $stats = app(\App\Services\ExpenseService::class)->getDashboardStats(auth()->id());
        $budgetSummary = app(\App\Services\BudgetService::class)->getDashboardSummary(auth()->id());
        $repo = app(ExpenseRepositoryInterface::class);
        $userId = auth()->id();

        $now = now();
        $configs = [
            '7d'  => ['days' => 7,   'label' => '7 Days'],
            '30d' => ['days' => 30,  'label' => '30 Days'],
            '3m'  => ['days' => 90,  'label' => '3 Months'],
            '6m'  => ['days' => 180, 'label' => '6 Months'],
            '1y'  => ['days' => 365, 'label' => '1 Year'],
        ];

        $allRaw = $repo->getDailyBreakdown(
            $userId,
            $now->copy()->subDays(365)->format('Y-m-d'),
            $now->format('Y-m-d')
        )->keyBy('date');

        $ranges = [];
        foreach ($configs as $key => $cfg) {
            $start = $now->copy()->subDays($cfg['days']);
            $prevEnd = $start->copy()->subDay();
            $prevStart = $prevEnd->copy()->subDays($cfg['days'])->addDay();

            $dailyData = [];
            $cursor = $start->copy();
            while ($cursor <= $now) {
                $ds = $cursor->format('Y-m-d');
                $found = $allRaw->get($ds);
                $dailyData[] = [
                    'date'  => $ds,
                    'total' => $found ? (float) $found->total : 0,
                    'count' => $found ? (int) $found->count : 0,
                ];
                $cursor->addDay();
            }

            $total = array_sum(array_column($dailyData, 'total'));
            $count = array_sum(array_column($dailyData, 'count'));
            $nonZero = array_values(array_filter($dailyData, fn($d) => $d['total'] > 0));
            $prevTotal = 0;
            $pCursor = $prevStart->copy();
            while ($pCursor <= $prevEnd) {
                $ds = $pCursor->format('Y-m-d');
                $found = $allRaw->get($ds);
                if ($found) $prevTotal += (float) $found->total;
                $pCursor->addDay();
            }

            $highest = null;
            $lowest = null;
            foreach ($nonZero as $d) {
                if (!$highest || $d['total'] > $highest['total']) $highest = $d;
                if (!$lowest  || $d['total'] < $lowest['total'])  $lowest = $d;
            }

            $ranges[$key] = [
                'dailyData'     => $dailyData,
                'total'         => $total,
                'count'         => $count,
                'previousTotal' => $prevTotal,
                'change'        => $prevTotal > 0 ? round(($total - $prevTotal) / $prevTotal * 100, 1) : 0,
                'highestDay'    => $highest,
                'lowestDay'     => $lowest,
                'avgDaily'      => count($nonZero) > 0 ? round($total / count($nonZero), 2) : 0,
                'label'         => $cfg['label'],
            ];
        }

        $stats['chartRanges'] = $ranges;
        $stats['budgetSummary'] = $budgetSummary;
        $stats['activeGoals'] = \App\Models\Goal::where('user_id', auth()->id())->where('status', 'active')->get();

        return view('dashboard', $stats);
    })->name('dashboard');

    Route::resource('expenses', ExpenseController::class);

    Route::get('/search', SearchController::class)->name('search');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::view('/advisor', 'advisor')->name('advisor');
    Route::post('/advisor/chat', App\Http\Controllers\FinancialAdvisorController::class)->name('advisor.chat');

    // New AI Advisor Module Routes
    Route::get('/api/advisor/weekly-summary', [\App\Http\Controllers\API\AdvisorController::class, 'getWeeklySummary']);
    Route::post('/api/advisor/ask', [\App\Http\Controllers\API\AdvisorController::class, 'ask'])->middleware('throttle:20,1');
});

require __DIR__.'/auth.php';
