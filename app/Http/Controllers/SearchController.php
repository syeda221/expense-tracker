<?php

namespace App\Http\Controllers;

use App\Services\ExpenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    private const FILTER_MAP = [
        'category' => 'category',
        'merchant' => 'merchant',
        'payment_method' => 'payment_method',
        'start_date' => 'date_from',
        'end_date' => 'date_to',
        'min_amount' => 'min_amount',
        'max_amount' => 'max_amount',
        'is_recurring' => 'is_recurring',
        'sort' => 'sort',
        'order' => 'order',
    ];

    public function __construct(
        private readonly ExpenseService $expenseService
    ) {}

    public function __invoke(Request $request): View|RedirectResponse
    {
        $query = $request->input('q');

        if (!$query) {
            return view('search', [
                'query' => null,
                'results' => collect(),
                'usedAi' => false,
                'summary' => null,
                'viewExpensesUrl' => null,
                'filters' => [],
            ]);
        }

        $aiResult = $this->expenseService->searchByAI($request->user()->id, $query);
        $filters = $aiResult['filters'];
        $usedAi = $aiResult['used_ai'];
        $summary = $aiResult['summary'];

        $shouldRedirect = $usedAi && preg_match('/^\s*show\b/i', $query);

        if ($shouldRedirect && $summary !== null && $summary['count'] > 0) {
            $urlParams = $this->buildUrlParams($filters);

            return redirect()->route('expenses.index', $urlParams);
        }

        $results = $this->expenseService->getPaginated($request->user()->id, $filters);
        $viewExpensesUrl = null;

        if ($usedAi && $summary !== null && $summary['count'] > 0) {
            $viewExpensesUrl = route('expenses.index', $this->buildUrlParams($filters));
        }

        return view('search', compact(
            'query', 'results', 'usedAi', 'summary', 'viewExpensesUrl', 'filters'
        ));
    }

    private function buildUrlParams(array $filters): array
    {
        $params = [];

        foreach ($filters as $key => $value) {
            $paramKey = self::FILTER_MAP[$key] ?? $key;
            $params[$paramKey] = $value;
        }

        return $params;
    }
}
