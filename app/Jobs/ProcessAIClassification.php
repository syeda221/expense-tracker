<?php

namespace App\Jobs;

use App\Models\Expense;
use App\Services\AI\AIManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ProcessAIClassification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $maxAttempts = 3;

    public function __construct(
        private readonly Expense $expense
    ) {}

    public function handle(AIManager $aiManager): void
    {
        if ($this->expense->ai_confidence !== null) {
            return;
        }

        DB::transaction(function () use ($aiManager) {
            $result = $aiManager->classify($this->expense->description);

            $this->expense->update([
                'category' => $result['category'],
                'merchant' => $result['merchant'],
                'ai_confidence' => $result['confidence'],
                'is_recurring' => $result['is_recurring'],
            ]);
        });
    }

    public function failed(\Throwable $e): void
    {
        DB::transaction(function () {
            $this->expense->update([
                'category' => config('ai.classifier.default_category', 'Other'),
                'ai_confidence' => config('ai.classifier.fallback_confidence', 0),
            ]);
        });
    }
}
