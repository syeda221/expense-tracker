<x-app-layout>
    @php
        $monthlyChange = $previousMonthTotal > 0 ? (($monthlyTotal - $previousMonthTotal) / $previousMonthTotal) * 100 : 0;
        $highestCatTotal = $categoryDistribution->isNotEmpty() ? $categoryDistribution->first()->total : 0;
        $highestCatName = $categoryDistribution->isNotEmpty() ? $categoryDistribution->first()->category : 'N/A';
    @endphp

    <div class="page-header fade-in">
        <h1 class="page-title">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ Auth::user()->name }}</h1>
        <p class="page-subtitle">Here's your financial overview for {{ now()->format('F Y') }}</p>
    </div>

    <div class="dashboard-stats fade-in-up stagger-1">
        <div class="card-premium hover-lift">
            <div class="card-body">
                <div class="stat-card">
                    <div class="stat-card-icon" style="background:var(--primary-subtle);color:var(--primary)">
                        <i data-lucide="wallet"></i>
                    </div>
                    <div class="stat-card-content">
                        <p class="stat-card-label">Total Expenses</p>
                        <h3 class="stat-card-value" style="color:var(--primary)">${{ number_format($yearlyTotal, 2) }}</h3>
                        <span class="stat-card-change {{ $monthlyChange >= 0 ? 'up' : 'down' }}">
                            <i data-lucide="{{ $monthlyChange >= 0 ? 'trending-up' : 'trending-down' }}" style="width:14px;height:14px"></i>
                            {{ number_format(abs($monthlyChange), 1) }}% vs last month
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-premium hover-lift">
            <div class="card-body">
                <div class="stat-card">
                    <div class="stat-card-icon" style="background:rgba(99,102,241,0.12);color:var(--secondary)">
                        <i data-lucide="calendar"></i>
                    </div>
                    <div class="stat-card-content">
                        <p class="stat-card-label">Monthly Spending</p>
                        <h3 class="stat-card-value" style="color:var(--secondary)">${{ number_format($monthlyTotal, 2) }}</h3>
                        <span class="stat-card-change neutral">{{ $monthlyCount }} transactions</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-premium hover-lift">
            <div class="card-body">
                <div class="stat-card">
                    <div class="stat-card-icon" style="background:var(--warning-subtle);color:var(--warning)">
                        <i data-lucide="trending-up"></i>
                    </div>
                    <div class="stat-card-content">
                        <p class="stat-card-label">Highest Category</p>
                        <h3 class="stat-card-value" style="color:var(--warning)">${{ number_format($highestCatTotal, 2) }}</h3>
                        <span class="stat-card-change neutral">{{ $highestCatName }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-premium hover-lift">
            <div class="card-body">
                <div class="stat-card">
                    <div class="stat-card-icon" style="background:var(--success-subtle);color:var(--success)">
                        <i data-lucide="bot"></i>
                    </div>
                    <div class="stat-card-content">
                        <p class="stat-card-label">AI Categorized</p>
                        <h3 class="stat-card-value" style="color:var(--success)">{{ $expenseCount }}</h3>
                        <span class="stat-card-change neutral">All expenses classified</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-premium hover-lift">
            <div class="card-body">
                <div class="stat-card">
                    <div class="stat-card-icon" style="background:rgba(168,85,247,0.12);color:var(--accent)">
                        <i data-lucide="repeat"></i>
                    </div>
                    <div class="stat-card-content">
                        <p class="stat-card-label">Recurring</p>
                        <h3 class="stat-card-value" style="color:var(--accent)">{{ $recurringCount }}</h3>
                        <span class="stat-card-change neutral">Active subscriptions</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-premium hover-lift">
            <div class="card-body">
                <div class="stat-card">
                    <div class="stat-card-icon" style="background:var(--danger-subtle);color:var(--danger)">
                        <i data-lucide="clock"></i>
                    </div>
                    <div class="stat-card-content">
                        <p class="stat-card-label">Latest Today</p>
                        <h3 class="stat-card-value" style="color:var(--danger)">${{ number_format($todayTotal, 2) }}</h3>
                        <span class="stat-card-change neutral">{{ $todayCount }} transactions</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-charts-top fade-in-up stagger-3" style="margin-top:24px">
        <div class="card-premium">
            <div class="card-header">
                <div class="widget-header" style="margin-bottom:0">
                    <h5 class="widget-title">
                        <i data-lucide="bar-chart-3"></i>
                        Monthly Spending — {{ now()->year }}
                    </h5>
                    <a href="{{ route('expenses.index') }}" class="widget-action">View all</a>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height:280px">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>

        <div class="card-premium">
            <div class="card-header">
                <div class="widget-header" style="margin-bottom:0">
                    <h5 class="widget-title">
                        <i data-lucide="pie-chart"></i>
                        Category Distribution
                    </h5>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height:280px;display:flex;align-items:center;justify-content:center">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-widgets fade-in-up stagger-4" style="margin-top:24px">
        <div class="card-premium">
            <div class="card-header">
                <div class="widget-header" style="margin-bottom:0">
                    <h5 class="widget-title">
                        <i data-lucide="list"></i>
                        Top Categories
                    </h5>
                </div>
            </div>
            <div class="card-body">
                @if ($categoryDistribution->isNotEmpty())
                    @php $grandTotal = $categoryDistribution->sum('total'); @endphp
                    @foreach ($categoryDistribution->take(6) as $cat)
                        @php $pct = $grandTotal > 0 ? ($cat->total / $grandTotal) * 100 : 0; @endphp
                        <div style="margin-bottom:16px">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
                                <span style="font-size:13px;font-weight:600;color:var(--text)">{{ $cat->category }}</span>
                                <span style="font-size:12px;color:var(--text-dim)">${{ number_format($cat->total, 2) }} · {{ number_format($pct, 1) }}%</span>
                            </div>
                            <div class="progress-premium">
                                <div class="progress-bar"
                                     style="width:{{ $pct }}%;background:{{ ['#8B5CF6','#6366F1','#A855F7','#22C55E','#F59E0B','#EF4444','#3B82F6','#EC4899'][$loop->index % 8] }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if ($categoryDistribution->count() > 6)
                        <div style="text-align:center;margin-top:8px">
                            <small style="color:var(--text-dim)">+{{ $categoryDistribution->count() - 6 }} more categories</small>
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon"><i data-lucide="folder-open"></i></div>
                        <p class="empty-state-title">No categories yet</p>
                        <p class="empty-state-text">Start adding expenses to see your category breakdown</p>
                        <a href="{{ route('expenses.create') }}" class="btn-premium btn-primary">Add Expense</a>
                    </div>
                @endif
            </div>
        </div>

        <div class="card-premium">
            <div class="card-header">
                <div class="widget-header" style="margin-bottom:0">
                    <h5 class="widget-title">
                        <i data-lucide="clock"></i>
                        Recent Transactions
                    </h5>
                    <a href="{{ route('expenses.index') }}" class="widget-action">View all</a>
                </div>
            </div>
            <div class="card-body" style="padding:0">
                @if ($recentExpenses->isNotEmpty())
                    <div style="padding:0">
                        @foreach ($recentExpenses as $expense)
                            <a href="{{ route('expenses.show', $expense) }}" style="display:flex;align-items:center;gap:12px;padding:14px 20px;text-decoration:none;transition:background var(--transition-fast);border-bottom:1px solid var(--border-light)">
                                <div style="width:36px;height:36px;border-radius:10px;background:var(--bg-hover);display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--text-dim)">
                                    @switch($expense->category)
                                        @case('Food & Dining')<i data-lucide="utensils" style="width:16px;height:16px"></i>@break
                                        @case('Shopping')<i data-lucide="shopping-bag" style="width:16px;height:16px"></i>@break
                                        @case('Transport')<i data-lucide="car" style="width:16px;height:16px"></i>@break
                                        @case('Groceries')<i data-lucide="shopping-cart" style="width:16px;height:16px"></i>@break
                                        @case('Bills')<i data-lucide="file-text" style="width:16px;height:16px"></i>@break
                                        @case('Utilities')<i data-lucide="zap" style="width:16px;height:16px"></i>@break
                                        @case('Healthcare')<i data-lucide="heart-pulse" style="width:16px;height:16px"></i>@break
                                        @case('Entertainment')<i data-lucide="film" style="width:16px;height:16px"></i>@break
                                        @case('Subscription')<i data-lucide="repeat" style="width:16px;height:16px"></i>@break
                                        @case('Travel')<i data-lucide="plane" style="width:16px;height:16px"></i>@break
                                        @default<i data-lucide="circle-dollar" style="width:16px;height:16px"></i>
                                    @endswitch
                                </div>
                                <div style="flex:1;min-width:0">
                                    <div style="font-size:14px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ Str::limit($expense->description, 35) }}</div>
                                    <div style="font-size:12px;color:var(--text-dim);display:flex;align-items:center;gap:8px">
                                        <span>{{ $expense->expense_date->format('M d, Y') }}</span>
                                        @if ($expense->merchant)
                                            <span>·</span>
                                            <span>{{ $expense->merchant }}</span>
                                        @endif
                                        @if ($expense->is_recurring)
                                            <span class="recurring-badge" style="font-size:10px">Recurring</span>
                                        @endif
                                    </div>
                                </div>
                                <div style="text-align:right;flex-shrink:0">
                                    <div style="font-size:15px;font-weight:700;color:var(--text)">${{ number_format($expense->amount, 2) }}</div>
                                    <span class="badge-premium category" style="font-size:10px">{{ $expense->category }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon"><i data-lucide="receipt"></i></div>
                        <p class="empty-state-title">No transactions yet</p>
                        <p class="empty-state-text">Add your first expense to get started</p>
                        <a href="{{ route('expenses.create') }}" class="btn-premium btn-primary">Add Expense</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="dashboard-bottom fade-in-up stagger-5" style="margin-top:24px">
        <div class="card-premium">
            <div class="card-header">
                <div class="widget-header" style="margin-bottom:0">
                    <h5 class="widget-title">
                        <i data-lucide="calendar"></i>
                        {{ now()->format('F') }} Summary
                    </h5>
                </div>
            </div>
            <div class="card-body">
                @php
                    $daysInMonth = now()->daysInMonth;
                    $today = now()->day;
                    $avgPerDay = $today > 0 ? $monthlyTotal / $today : 0;
                    $projectedTotal = $avgPerDay * $daysInMonth;
                @endphp
                <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px">
                    <div style="text-align:center;padding:12px;background:var(--bg-hover);border-radius:12px">
                        <p style="font-size:11px;color:var(--text-dim);margin:0 0 4px;font-weight:500">Monthly Total</p>
                        <h4 style="color:var(--success);margin:0;font-size:20px">${{ number_format($monthlyTotal, 2) }}</h4>
                    </div>
                    <div style="text-align:center;padding:12px;background:var(--bg-hover);border-radius:12px">
                        <p style="font-size:11px;color:var(--text-dim);margin:0 0 4px;font-weight:500">Daily Avg</p>
                        <h4 style="color:var(--secondary);margin:0;font-size:20px">${{ number_format($avgPerDay, 2) }}</h4>
                    </div>
                    <div style="text-align:center;padding:12px;background:var(--bg-hover);border-radius:12px">
                        <p style="font-size:11px;color:var(--text-dim);margin:0 0 4px;font-weight:500">Projected</p>
                        <h4 style="color:var(--warning);margin:0;font-size:20px">${{ number_format($projectedTotal, 2) }}</h4>
                    </div>
                    <div style="text-align:center;padding:12px;background:var(--bg-hover);border-radius:12px">
                        <p style="font-size:11px;color:var(--text-dim);margin:0 0 4px;font-weight:500">Budget Used</p>
                        <h4 style="color:var(--accent);margin:0;font-size:20px">{{ $today > 0 ? number_format(($today / $daysInMonth) * 100, 0) : 0 }}%</h4>
                    </div>
                </div>

                @if ($dailyBreakdown->isNotEmpty())
                    <div>
                        <div style="display:flex;justify-content:space-between;margin-bottom:10px">
                            <span style="font-size:12px;color:var(--text-dim);font-weight:500">Daily spending activity</span>
                            <span style="font-size:12px;color:var(--text-dim)">{{ $today }} of {{ $daysInMonth }} days</span>
                        </div>
                        <div style="display:flex;align-items:flex-end;gap:3px;height:80px">
                            @php $maxDaily = $dailyBreakdown->max('total') ?: 1; @endphp
                            @foreach ($dailyBreakdown as $day)
                                @php
                                    $h = max(4, ($day->total / $maxDaily) * 72);
                                    $isToday = $day->date == today()->toDateString();
                                @endphp
                                <div style="flex:1;text-align:center;position:relative" title="{{ \Carbon\Carbon::parse($day->date)->format('M d') }}: ${{ number_format($day->total, 2) }}">
                                    <div style="height:{{ $h }}px;width:100%;border-radius:4px 4px 0 0;background:{{ $isToday ? 'linear-gradient(180deg,var(--primary),var(--accent))' : 'var(--bg-hover)' }};transition:background var(--transition-fast);min-height:4px"></div>
                                    <small style="font-size:8px;color:var(--text-dim);display:block;margin-top:2px">{{ \Carbon\Carbon::parse($day->date)->format('d') }}</small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="empty-state" style="padding:24px">
                        <p style="color:var(--text-dim);font-size:13px;margin:0">No expenses this month</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        Chart.defaults.color = '#94A3B8';
        Chart.defaults.borderColor = 'rgba(255,255,255,0.08)';

        const monthlyData = @json($monthlySpending);
        const categoryData = @json($categoryDistribution);

        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const monthlyValues = new Array(12).fill(0);
        monthlyData.forEach(function (d) { monthlyValues[d.month - 1] = parseFloat(d.total); });

        const barCtx = document.getElementById('monthlyChart').getContext('2d');
        const gradient = barCtx.createLinearGradient(0, 0, 0, 280);
        gradient.addColorStop(0, 'rgba(139, 92, 246, 0.6)');
        gradient.addColorStop(1, 'rgba(139, 92, 246, 0.05)');

        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'Spending',
                    data: monthlyValues,
                    backgroundColor: gradient,
                    borderColor: '#8B5CF6',
                    borderWidth: 1,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1f1f23',
                        titleColor: '#F8FAFC',
                        bodyColor: '#94A3B8',
                        borderColor: 'rgba(255,255,255,0.08)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function (ctx) {
                                return '$' + parseFloat(ctx.raw).toLocaleString('en-US', { minimumFractionDigits: 2 });
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255,255,255,0.04)' },
                        ticks: { callback: function (v) { return '$' + v; } }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });

        const colors = ['#8B5CF6','#6366F1','#A855F7','#22C55E','#F59E0B','#EF4444','#3B82F6','#EC4899','#14B8A6','#F97316'];

        if (categoryData.length > 0) {
            new Chart(document.getElementById('categoryChart'), {
                type: 'doughnut',
                data: {
                    labels: categoryData.map(function (d) {
                        return d.category + ' ($' + new Intl.NumberFormat('en-US', { minimumFractionDigits: 0 }).format(d.total) + ')';
                    }),
                    datasets: [{
                        data: categoryData.map(function (d) { return parseFloat(d.total); }),
                        backgroundColor: colors.slice(0, categoryData.length),
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#94A3B8',
                                boxWidth: 8,
                                padding: 8,
                                font: { size: 11 }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1f1f23',
                            titleColor: '#F8FAFC',
                            bodyColor: '#94A3B8',
                            borderColor: 'rgba(255,255,255,0.08)',
                            borderWidth: 1,
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: function (ctx) {
                                    var total = ctx.dataset.data.reduce(function (a, b) { return a + b; }, 0);
                                    var pct = ((ctx.raw / total) * 100).toFixed(1);
                                    return ctx.label + ' (' + pct + '%)';
                                }
                            }
                        }
                    }
                }
            });
        } else {
            document.getElementById('categoryChart').parentElement.innerHTML = '<p style="color:var(--text-dim);font-size:13px;text-align:center">No data</p>';
        }
    });
    </script>
    @endpush
</x-app-layout>