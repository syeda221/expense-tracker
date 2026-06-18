<?php

namespace App\Http\Controllers;

use App\Services\ExpenseService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __construct(
        private readonly ExpenseService $expenseService
    ) {}

    public function __invoke(Request $request): View
    {
        $query = $request->input('q');
        $results = collect();
        $usedAi = false;

        if ($query) {
            $aiResult = $this->expenseService->searchByAI($request->user()->id, $query);
            $results = $this->expenseService->getPaginated(
                $request->user()->id,
                $aiResult['filters']
            );
            $usedAi = $aiResult['used_ai'];
        }

        return view('search', compact('query', 'results', 'usedAi'));
    }
}
