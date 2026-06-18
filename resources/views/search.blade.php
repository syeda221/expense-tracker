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
                <button type="button" class="search-example-btn" data-query="How much did I spend on food?">Food expenses</button>
                <button type="button" class="search-example-btn" data-query="Entertainment expenses this month">Entertainment</button>
                <button type="button" class="search-example-btn" data-query="Fuel expenses above 5000">Fuel above 5000</button>
                <button type="button" class="search-example-btn" data-query="Show shopping by card">Shopping by card</button>
                <button type="button" class="search-example-btn" data-query="Show Uber expenses">Uber expenses</button>
                <button type="button" class="search-example-btn" data-query="Bills between January and March">Bills Jan–Mar</button>
                <button type="button" class="search-example-btn" data-query="Netflix subscriptions">Subscriptions</button>
                <button type="button" class="search-example-btn" data-query="Highest expense this year">Highest this year</button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
    document.querySelector('.search-examples')?.addEventListener('click', function (e) {
        var btn = e.target.closest('.search-example-btn');
        if (!btn) return;
        e.preventDefault();
        var q = btn.getAttribute('data-query');
        if (!q) return;
        var input = document.querySelector('.search-box input[name="q"]');
        if (input) input.value = q;
        btn.closest('form').submit();
    });
    </script>
    @endpush

    @if ($query)
        <div class="fade-in-up" style="margin-top:32px">
            <div class="card-premium" style="margin-bottom:20px">
                <div class="card-body" style="padding:16px 24px">
                    <div style="display:flex;align-items:center;gap:10px">
                        <span class="badge-premium ai">AI</span>
                        <span style="font-size:14px;color:var(--text-muted)">Search results for:</span>
                        <strong style="font-size:15px;color:var(--text)">"{{ $query }}"</strong>
                    </div>
                </div>
            </div>

            @if ($summary && $summary['count'] > 0)
                @php
                    $label = $filters['category'] ?? $filters['merchant'] ?? 'Matching';
                @endphp
                <div class="card-premium" style="margin-bottom:20px;border-left:4px solid var(--primary)">
                    <div class="card-body">
                        <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap">
                            <div style="width:56px;height:56px;border-radius:14px;background:var(--primary-subtle);display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--primary)">
                                <i data-lucide="chart-pie" style="width:28px;height:28px"></i>
                            </div>
                            <div style="flex:1;min-width:0">
                                <h3 style="margin:0 0 4px;font-size:20px;font-weight:700;color:var(--text)">{{ $label }} Expenses</h3>
                                <p style="margin:0;font-size:13px;color:var(--text-dim)">AI-powered summary based on your search</p>
                            </div>
                            <div style="display:flex;gap:32px;flex-shrink:0">
                                <div style="text-align:center">
                                    <p style="margin:0 0 2px;font-size:11px;color:var(--text-dim);font-weight:500;text-transform:uppercase;letter-spacing:0.5px">Total Spent</p>
                                    <p style="margin:0;font-size:22px;font-weight:800;color:var(--success)">${{ number_format($summary['total'], 2) }}</p>
                                </div>
                                <div style="text-align:center">
                                    <p style="margin:0 0 2px;font-size:11px;color:var(--text-dim);font-weight:500;text-transform:uppercase;letter-spacing:0.5px">Transactions</p>
                                    <p style="margin:0;font-size:22px;font-weight:800;color:var(--text)">{{ $summary['count'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($viewExpensesUrl)
                    <div style="text-align:center;margin-top:20px">
                        <a href="{{ $viewExpensesUrl }}" class="btn-premium btn-primary lg" style="justify-content:center">
                            <i data-lucide="list"></i>
                            View Expense Records
                        </a>
                    </div>
                @endif
            @elseif ($usedAi && $summary !== null && $summary['count'] === 0)
                <div class="card-premium">
                    <div class="empty-state">
                        <div class="empty-state-icon"><i data-lucide="search-x"></i></div>
                        <p class="empty-state-title">No matching expenses</p>
                        <p class="empty-state-text">Try a different search or adjust your query</p>
                        <a href="{{ route('expenses.index') }}" class="btn-premium btn-secondary">
                            <i data-lucide="list"></i>
                            View All Expenses
                        </a>
                    </div>
                </div>
            @elseif (!$usedAi && $results->isNotEmpty())
                <div class="card-premium">
                    <div class="card-body" style="padding:16px 24px">
                        <p style="margin:0;font-size:14px;color:var(--text-muted);text-align:center">
                            AI could not parse your query. Showing keyword-based results instead.
                        </p>
                    </div>
                </div>
                <div style="margin-top:16px">
                    @foreach ($results as $expense)
                        <a href="{{ route('expenses.show', $expense) }}" style="display:flex;align-items:center;gap:16px;padding:16px 20px;background:var(--bg-card);border:1px solid var(--border);border-radius:12px;text-decoration:none;transition:all var(--transition-fast);margin-bottom:8px">
                            <div style="width:40px;height:40px;border-radius:10px;background:var(--bg-hover);display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--text-dim)">
                                <i data-lucide="circle-dollar" style="width:18px;height:18px"></i>
                            </div>
                            <div style="flex:1;min-width:0">
                                <div style="font-size:15px;font-weight:600;color:var(--text);margin-bottom:2px">{{ Str::limit($expense->description, 40) }}</div>
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
                </div>
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