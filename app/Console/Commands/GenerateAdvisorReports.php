<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Expense;
use App\Models\AdvisorInsight;
use App\Services\SpendingAnalyticsService;
use App\Services\AdvisorPromptBuilder;
use App\Services\AI\GroqService;

class GenerateAdvisorReports extends Command
{
    protected $signature = 'advisor:generate-reports {type=weekly : The type of report to generate (weekly or monthly)}';
    protected $description = 'Generate weekly or monthly AI Advisor reports for all active users';

    public function handle(
        SpendingAnalyticsService $analytics, 
        AdvisorPromptBuilder $promptBuilder, 
        GroqService $groq
    ) {
        $type = $this->argument('type');
        $this->info("Generating {$type} advisor reports...");

        $users = User::all();

        foreach ($users as $user) {
            if (Expense::where('user_id', $user->id)->count() === 0) {
                continue;
            }

            try {
                if ($type === 'weekly') {
                    $data = $analytics->getWeeklySummary($user->id);
                    $title = 'Your Week in Review';
                    $insightType = 'weekly_summary';
                } else {
                    $data = $analytics->getMonthlyProgress($user->id);
                    $title = 'Your Monthly Progress';
                    $insightType = 'monthly_summary';
                }

                $systemPrompt = $promptBuilder->buildSystemPrompt();
                $userMessage = $promptBuilder->buildUserMessage($data, "Generate a friendly {$type} summary based on this data.");

                $aiResponse = $groq->getAdvisorResponse([
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userMessage],
                ]);

                AdvisorInsight::create([
                    'user_id' => $user->id,
                    'type' => $insightType,
                    'title' => $title,
                    'message' => $aiResponse,
                    'data' => $data,
                ]);

                $this->info("Generated report for user ID: {$user->id}");
            } catch (\Exception $e) {
                $this->error("Failed to generate report for user ID: {$user->id}. Error: " . $e->getMessage());
                \Log::error("Advisor report generation error for user {$user->id}: " . $e->getMessage());
            }
        }

        $this->info("Finished generating {$type} reports.");
    }
}
