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
    private \App\Services\AI\AICopilotService $copilot;

    public function __construct(SpendingAnalyticsService $analytics, AdvisorPromptBuilder $promptBuilder, GroqService $groq, \App\Services\AI\AICopilotService $copilot)
    {
        $this->analytics = $analytics;
        $this->promptBuilder = $promptBuilder;
        $this->groq = $groq;
        $this->copilot = $copilot;
    }

    public function getWeeklySummary(Request $request)
    {
        $user = Auth::user();
        
        if (Expense::where('user_id', $user->id)->count() === 0) {
            return response()->json([
                'success' => true,
                'message' => 'Not enough spending data yet. Add a few expenses and Foresight will start giving personalized insights.'
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
                'message' => 'Foresight is thinking too hard right now. Please try again in a little while.'
            ], 500);
        }
    }

    public function ask(Request $request)
    {
        $request->validate(['question' => 'required|string|max:500']);
        $user = Auth::user();
        $question = $request->question;

        $conversation = AdvisorConversation::firstOrCreate(
            ['user_id' => $user->id],
            ['title' => 'Chat with Foresight']
        );

        AdvisorMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'message' => $question
        ]);

        try {
            $result = $this->copilot->processUserMessage($user, $question);

            AdvisorMessage::create([
                'conversation_id' => $conversation->id,
                'role' => 'assistant',
                'message' => $result['response']
            ]);

            return response()->json([
                'success' => true,
                'response' => $result['response'],
                'should_refresh' => $result['should_refresh']
            ]);
        } catch (\Exception $e) {
            \Log::error('Advisor ask error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Foresight is thinking too hard right now. Please try again in a little while.'
            ], 500);
        }
    }
}
