<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Jobs\ProcessAIClassification;
use App\Models\Expense;
use App\Services\ExpenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function __construct(
        private readonly ExpenseService $expenseService
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'category', 'merchant', 'payment_method', 'date_from', 'date_to']);
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

        $expense = $this->expenseService->create($data);

        ProcessAIClassification::dispatch($expense);

        return to_route('expenses.index')
            ->with('success', 'Expense created successfully. AI is analyzing it now.');
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

        $this->expenseService->update($expense, $request->validated());

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
