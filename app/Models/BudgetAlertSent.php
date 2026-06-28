<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetAlertSent extends Model
{
    protected $table = 'budget_alerts_sent';

    protected $fillable = [
        'user_id',
        'budget_id',
        'threshold_percent',
        'period_start',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'threshold_percent' => 'decimal:1',
            'period_start' => 'date',
            'sent_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }
}
