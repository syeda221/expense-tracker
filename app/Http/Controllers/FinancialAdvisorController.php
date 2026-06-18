<?php

namespace App\Http\Controllers;

use App\Services\FinancialAdvisor\FinancialAdvisorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FinancialAdvisorController extends Controller
{
    public function __construct(
        private readonly FinancialAdvisorService $advisorService,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $request->validate(['message' => 'required|string|max:1000']);

        $userId = auth()->id();
        if (!$userId) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $result = $this->advisorService->process($userId, $request->input('message'));

        return response()->json([
            'reply' => $result['response'],
            'type' => $result['type'] ?? 'help',
        ]);
    }
}
