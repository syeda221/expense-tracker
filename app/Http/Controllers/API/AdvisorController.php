<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AdvisorConversation;
use App\Models\AdvisorInsight;
use App\Models\AdvisorMessage;
use App\Models\Budget;
use App\Models\Expense;
use App\Services\AdvisorPromptBuilder;
use App\Services\AI\GroqService;
use App\Services\SpendingAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdvisorController extends Controller
{
    private SpendingAnalyticsService $analytics;
    private AdvisorPromptBuilder $promptBuilder;
    private GroqService $groq;

    public function __construct(SpendingAnalyticsService $analytics, AdvisorPromptBuilder $promptBuilder, GroqService $groq)
    {
        $this->analytics = $analytics;
        $this->promptBuilder = $promptBuilder;
        $this->groq = $groq;
    }

    public function getWeeklySummary(Request $request)
    {
        $user = Auth::user();
        
        if (Expense::where('user_id', $user->id)->count() === 0) {
            return response()->json([
                'success' => true,
                'message' => 'Not enough spending data yet. Add a few expenses and Ollie will start giving personalized insights.'
            ]);
        }

        $data = $this->analytics->getWeeklySummary($user->id);
        
        $systemPrompt = $this->promptBuilder->buildSystemPrompt();
        $userMessage = $this->promptBuilder->buildUserMessage($data);

        try {
            $aiResponse = $this->groq->getAdvisorResponse([
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userMessage],
            ]);

            $insight = AdvisorInsight::create([
                'user_id' => $user->id,
                'type' => 'weekly_summary',
                'title' => 'Your Week in Review',
                'message' => $aiResponse,
                'data' => $data,
            ]);

            return response()->json([
                'success' => true,
                'insight' => $insight
            ]);
        } catch (\Exception $e) {
            \Log::error('Advisor weekly summary error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ollie is thinking too hard right now. Please try again in a little while.'
            ], 500);
        }
    }

    public function ask(Request $request)
    {
        $request->validate(['question' => 'required|string|max:500']);
        $user = Auth::user();
        $question = $request->question;

        // 1. Intent Detection for Budgets
        $intentData = $this->groq->detectIntent($question);
        $intent = $intentData['intent'] ?? '';
        
        if (in_array($intent, ['set_budget', 'add_budget']) && isset($intentData['amount'])) {
            $category = $intentData['category'] ?? 'General';
            if (strtolower($category) === 'general' || strtolower($category) === 'overall') {
                $category = null;
            }
            $budget = Budget::firstOrNew([
                'user_id' => $user->id,
                'category' => $category,
            ]);
            
            if ($intent === 'add_budget') {
                $budget->amount = ($budget->amount ?? 0) + $intentData['amount'];
            } else {
                $budget->amount = $intentData['amount'];
            }
            
            $budget->period_type = $intentData['period'] ?? 'monthly';
            if (!$budget->exists) {
                $budget->period_start = now()->startOfMonth();
            }
            $budget->save();
            
            return response()->json([
                'success' => true,
                'message' => "I've successfully updated your budget for " . $category . " to RS " . $budget->amount . "!"
            ]);
        }

        // 2. Normal Chat
        $conversation = AdvisorConversation::firstOrCreate(
            ['user_id' => $user->id],
            ['title' => 'Chat with Ollie']
        );

        AdvisorMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'message' => $question
        ]);

        $data = [
            'weekly_summary' => $this->analytics->getWeeklySummary($user->id),
            'monthly_progress' => $this->analytics->getMonthlyProgress($user->id),
            'budgets' => Budget::where('user_id', $user->id)->get()->map(function($b) {
                return ['category' => $b->category ?? 'Overall', 'amount' => $b->amount];
            })->toArray(),
        ];
        
        $systemPrompt = $this->promptBuilder->buildSystemPrompt();
        $userMessage = $this->promptBuilder->buildUserMessage($data, $question);

        $messages = [['role' => 'system', 'content' => $systemPrompt]];
        
        // Add last 5 history messages
        $history = AdvisorMessage::where('conversation_id', $conversation->id)
            ->latest()
            ->take(5)
            ->get()
            ->reverse();
            
        foreach ($history as $msg) {
            $messages[] = ['role' => $msg->role, 'content' => $msg->message];
        }
        
        $messages[] = ['role' => 'user', 'content' => $userMessage];

        try {
            $aiResponse = $this->groq->getAdvisorResponse($messages);

            AdvisorMessage::create([
                'conversation_id' => $conversation->id,
                'role' => 'assistant',
                'message' => $aiResponse
            ]);

            return response()->json([
                'success' => true,
                'response' => $aiResponse
            ]);
        } catch (\Exception $e) {
            \Log::error('Advisor ask error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ollie is thinking too hard right now. Please try again in a little while.'
            ], 500);
        }
    }
}
