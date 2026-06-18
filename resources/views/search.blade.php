<x-app-layout>
    <div class="search-hero fade-in">
        <h1>AI Search</h1>
        <p>Ask anything about your expenses — natural language queries powered by AI</p>

        <form method="GET" action="{{ route('search') }}" class="search-box">
            <div class="input-wrapper">
                <i data-lucide="search" class="search-icon"></i>
                <input type="text" name="q" value="{{ $query ?? '' }}" placeholder="Ask AI about your expenses..." autofocus autocomplete="off">
            </div>
            <div class="search-examples">
                <button type="submit" name="q" value="How much did I spend on food?" class="search-example-btn" onclick="this.form.submit()">How much did I spend on food?</button>
                <button type="submit" name="q" value="Show Uber expenses" class="search-example-btn" onclick="this.form.submit()">Show Uber expenses</button>
                <button type="submit" name="q" value="This month's shopping" class="search-example-btn" onclick="this.form.submit()">This month's shopping</button>
                <button type="submit" name="q" value="Highest expenses this year" class="search-example-btn" onclick="this.form.submit()">Highest expenses this year</button>
            </div>
        </form>
    </div>

    @if ($query)
        <div class="fade-in-up" style="margin-top:32px">
            <div class="card-premium" style="margin-bottom:20px">
                <div class="card-body" style="padding:16px 24px">
                    <div style="display:flex;align-items:center;gap:10px">
                        <span class="badge-premium ai">AI</span>
                        <span style="font-size:14px;color:var(--text-muted)">Search results for:</span>
                        <strong style="font-size:15px;color:var(--text)">"{{ $query }}"</strong>
                        <span style="margin-left:auto;font-size:13px;color:var(--text-dim)">{{ $results->total() }} result{{ $results->total() !== 1 ? 's' : '' }}</span>
                    </div>
                </div>
            </div>

            @if ($results->isNotEmpty())
                @foreach ($results as $expense)
                    <a href="{{ route('expenses.show', $expense) }}" style="display:flex;align-items:center;gap:16px;padding:16px 20px;background:var(--bg-card);border:1px solid var(--border);border-radius:12px;text-decoration:none;transition:all var(--transition-fast);margin-bottom:8px">
                        <div style="width:40px;height:40px;border-radius:10px;background:var(--bg-hover);display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--text-dim)">
                            <i data-lucide="circle-dollar" style="width:18px;height:18px"></i>
                        </div>
                        <div style="flex:1;min-width:0">
                            <div style="font-size:15px;font-weight:600;color:var(--text);margin-bottom:2px">{{ $expense->description }}</div>
                            <div style="font-size:12px;color:var(--text-dim);display:flex;align-items:center;gap:10px">
                                <span>{{ $expense->expense_date->format('M d, Y') }}</span>
                                <span>·</span>
                                <span>{{ $expense->merchant ?? 'N/A' }}</span>
                                <span>·</span>
                                <span>{{ $expense->payment_method }}</span>
                            </div>
                        </div>
                        <div style="text-align:right;flex-shrink:0">
                            <div style="font-size:16px;font-weight:700;color:var(--text);margin-bottom:4px">${{ number_format($expense->amount, 2) }}</div>
                            <span class="badge-premium category" style="font-size:11px">{{ $expense->category }}</span>
                        </div>
                        <i data-lucide="chevron-right" style="width:18px;height:18px;color:var(--text-dim);flex-shrink:0"></i>
                    </a>
                @endforeach

                @if ($results->hasPages())
                    <div style="margin-top:20px;display:flex;justify-content:center">
                        {{ $results->withQueryString()->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            @else
                <div class="card-premium">
                    <div class="empty-state">
                        <div class="empty-state-icon"><i data-lucide="search-x"></i></div>
                        <p class="empty-state-title">No results found</p>
                        <p class="empty-state-text">Try different keywords or browse your expenses</p>
                        <a href="{{ route('expenses.index') }}" class="btn-premium btn-secondary">
                            <i data-lucide="list"></i>
                            View All Expenses
                        </a>
                    </div>
                </div>
            @endif
        </div>
    @endif
</x-app-layout>