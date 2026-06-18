<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Expense;
use App\Services\AI\AIManager;
use App\Services\BudgetService;
use App\Services\ExpenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function __construct(
        private readonly ExpenseService $expenseService,
        private readonly AIManager $aiManager,
        private readonly BudgetService $budgetService,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'category', 'merchant', 'payment_method', 'date_from', 'date_to', 'min_amount', 'max_amount', 'is_recurring', 'sort', 'order']);
        $expenses = $this->expenseService->getPaginated($request->user()->id, $filters);

        return view('expenses.index', compact('expenses', 'filters'));
    }

    public function create(): View
    {
        return view('expenses.create');
    }

    public function store(StoreExpenseRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['category'] = $data['category'] ?? 'Other';

        $expense = $this->expenseService->create($data);

        try {
            $result = $this->aiManager->classify($expense->description);
            $expense->update([
                'category' => $result['category'],
                'merchant' => $result['merchant'],
                'ai_confidence' => $result['confidence'],
                'is_recurring' => $result['is_recurring'],
            ]);
        } catch (\Throwable $e) {
            $expense->update([
                'category' => config('ai.classifier.default_category', 'Other'),
                'ai_confidence' => config('ai.classifier.fallback_confidence', 0),
            ]);
        }

        $alerts = $this->budgetService->checkAlerts($request->user()->id, $expense->category, (float) $expense->amount);
        $alertMessages = [];
        foreach ($alerts as $alert) {
            $alertMessages[] = $alert['message'];
        }

        $flash = ['success' => 'Expense saved and categorized by AI.'];
        if (!empty($alertMessages)) {
            $flash['warning'] = implode(' ', $alertMessages);
        }

        return to_route('expenses.show', $expense)->with($flash);
    }

    public function show(Request $request, Expense $expense): View
    {
        if ($expense->user_id !== $request->user()->id) {
            abort(403);
        }

        return view('expenses.show', compact('expense'));
    }

    public function edit(Request $request, Expense $expense): View
    {
        if ($expense->user_id !== $request->user()->id) {
            abort(403);
        }

        return view('expenses.edit', compact('expense'));
    }

    public function update(UpdateExpenseRequest $request, Expense $expense): RedirectResponse
    {
        if ($expense->user_id !== $request->user()->id) {
            abort(403);
        }

        $data = $request->validated();
        $data['category'] = $data['category'] ?? $expense->category;

        $this->expenseService->update($expense, $data);

        return to_route('expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Request $request, Expense $expense): RedirectResponse
    {
        if ($expense->user_id !== $request->user()->id) {
            abort(403);
        }

        $this->expenseService->delete($expense);

        return to_route('expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }
}
