<x-app-layout>
    @php
        $monthlyChange = $previousMonthTotal > 0 ? (($monthlyTotal - $previousMonthTotal) / $previousMonthTotal) * 100 : 0;
        $highestCatTotal = $categoryDistribution->isNotEmpty() ? $categoryDistribution->first()->total : 0;
        $highestCatName = $categoryDistribution->isNotEmpty() ? $categoryDistribution->first()->category : 'N/A';
    @endphp

    <div class="page-header fade-in" style="position:relative;display:flex;flex-direction:column;align-items:center;text-align:center;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:140px 24px 40px;box-shadow:var(--shadow-card);margin-top:80px;margin-bottom:40px;overflow:visible;">
        <div class="owl-container" style="position:absolute;top:-80px;left:50%;transform:translateX(-50%);z-index:3;width:200px;height:240px;display:flex;align-items:flex-end;justify-content:center;pointer-events:none;">
            <!-- Soft radial glow behind mascot -->
            <div style="position:absolute;bottom:20px;width:240px;height:240px;background:radial-gradient(circle, rgba(22,199,183,0.15) 0%, rgba(22,199,183,0) 70%);border-radius:50%;z-index:-1;"></div>
            
            <video autoplay loop muted playsinline class="owl-video" style="width:100%;height:100%;object-fit:cover;object-position:bottom;pointer-events:auto;">
                <source src="{{ asset('video/Mascot_placing_wing_on_chin_202606242120.mp4') }}" type="video/mp4">
            </video>
            
            <div style="position:absolute;top:40px;right:-10px;background:var(--primary);color:#fff;font-size:11px;font-weight:800;padding:4px 10px;border-radius:20px;box-shadow:0 4px 12px rgba(22,199,183,0.3);white-space:nowrap;transform:rotate(5deg);pointer-events:auto;">AI Active</div>
        </div>

        <div style="position:relative;z-index:2">
            <h1 class="page-title" style="margin:0;font-size:32px;font-weight:800;letter-spacing:-0.02em;">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ Auth::user()->name }}!</h1>
            <p class="page-subtitle" style="margin:12px auto 0;font-size:16px;color:var(--text-muted);line-height:1.6;max-width:400px;">I've got your financial overview ready.<br>Let's make it a great day!</p>
        </div>
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
                        <h3 class="stat-card-value" style="color:var(--primary)">RS {{ number_format($yearlyTotal, 2) }}</h3>
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
                        <h3 class="stat-card-value" style="color:var(--secondary)">RS {{ number_format($monthlyTotal, 2) }}</h3>
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
                        <h3 class="stat-card-value" style="color:var(--warning)">RS {{ number_format($highestCatTotal, 2) }}</h3>
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
                    <div class="stat-card-icon" style="background:var(--primary-subtle);color:var(--primary)">
                        <i data-lucide="repeat"></i>
                    </div>
                    <div class="stat-card-content">
                        <p class="stat-card-label">Recurring</p>
                        <h3 class="stat-card-value" style="color:var(--primary)">{{ $recurringCount }}</h3>
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
                        <h3 class="stat-card-value" style="color:var(--danger)">RS {{ number_format($todayTotal, 2) }}</h3>
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
                                <span style="font-size:12px;color:var(--text-dim)">RS {{ number_format($cat->total, 2) }} · {{ number_format($pct, 1) }}%</span>
                            </div>
                            <div class="progress-premium">
                                <div class="progress-bar"
                                     style="width:{{ $pct }}%;background:{{ ['#0ECFB3','#0AA896','#38C8F5','#23D97A','#FFB547','#FF5E6C','#3B82F6','#EC4899'][$loop->index % 8] }}">
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
                        <div class="owl-container" style="margin-bottom: 16px;">
                            <video autoplay loop muted playsinline class="owl-video" style="width:160px;height:160px;margin:-16px 0">
                                <source src="{{ asset('video/Mascot_placing_wing_on_chin_202606242120.mp4') }}" type="video/mp4">
                            </video>
                        </div>
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
                                    <div style="font-size:15px;font-weight:700;color:var(--text)">RS {{ number_format($expense->amount, 2) }}</div>
                                    <span class="badge-premium category" style="font-size:10px">{{ $expense->category }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="owl-container" style="margin-bottom: 16px;">
                            <video autoplay loop muted playsinline class="owl-video" style="width:160px;height:160px;margin:-16px 0">
                                <source src="{{ asset('video/Mascot_placing_wing_on_chin_202606242120.mp4') }}" type="video/mp4">
                            </video>
                        </div>
                        <p class="empty-state-title">No transactions yet</p>
                        <p class="empty-state-text">Add your first expense to get started</p>
                        <a href="{{ route('expenses.create') }}" class="btn-premium btn-primary">Add Expense</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Budget summary card --}}
    @if ($budgetSummary['has_budget'] ?? false)
    <div class="dashboard-widgets fade-in-up stagger-5" style="margin-top:24px">
        <div class="card-premium">
            <div class="card-header">
                <div class="widget-header" style="margin-bottom:0">
                    <h5 class="widget-title">
                        <i data-lucide="target"></i>
                        Budget Overview
                    </h5>
                    <a href="{{ route('advisor') }}" class="widget-action">View Details</a>
                </div>
            </div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 2fr;gap:24px;align-items:center">
                    {{-- Progress ring --}}
                    <div style="text-align:center">
                        @php
                            $overall = $budgetSummary['overall'] ?? [];
                            $pct = $overall['percentage'] ?? 0;
                            $spent = $overall['spent'] ?? 0;
                            $budget = $overall['budget'] ?? 0;
                            $remaining = $overall['remaining'] ?? 0;
                            $ringColor = $pct >= 100 ? 'var(--danger)' : ($pct >= 80 ? 'var(--warning)' : 'var(--primary)');
                            $circum = 2 * pi() * 42;
                            $offset = $circum - ($pct / 100) * $circum;
                        @endphp
                        <svg width="110" height="110" viewBox="0 0 100 100" style="display:block;margin:0 auto">
                            <circle cx="50" cy="50" r="42" fill="none" stroke="var(--border-subtle)" stroke-width="8"/>
                            <circle cx="50" cy="50" r="42" fill="none" stroke="{{ $ringColor }}" stroke-width="8"
                                    stroke-linecap="round"
                                    stroke-dasharray="{{ $circum }}"
                                    stroke-dashoffset="{{ max(0, $offset) }}"
                                    transform="rotate(-90 50 50)"
                                    style="transition:stroke-dashoffset 0.6s ease"/>
                        </svg>
                        <div style="margin-top:8px">
                            <span style="font-size:22px;font-weight:800;color:{{ $ringColor }};font-family:var(--font-mono)">{{ $pct }}%</span>
                            <p style="margin:2px 0 0;font-size:11px;color:var(--text-dim)">used</p>
                        </div>
                    </div>
                    {{-- Details --}}
                    <div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                            <div style="padding:12px;background:var(--bg-hover);border-radius:8px">
                                <p style="margin:0 0 2px;font-size:11px;color:var(--text-dim)">Budget</p>
                                <p style="margin:0;font-size:18px;font-weight:700;color:var(--text);font-family:var(--font-mono)">RS {{ number_format($budget, 2) }}</p>
                            </div>
                            <div style="padding:12px;background:var(--bg-hover);border-radius:8px">
                                <p style="margin:0 0 2px;font-size:11px;color:var(--text-dim)">Spent</p>
                                <p style="margin:0;font-size:18px;font-weight:700;color:var(--primary);font-family:var(--font-mono)">RS {{ number_format($spent, 2) }}</p>
                            </div>
                            <div style="padding:12px;background:var(--bg-hover);border-radius:8px">
                                <p style="margin:0 0 2px;font-size:11px;color:var(--text-dim)">Remaining</p>
                                <p style="margin:0;font-size:18px;font-weight:700;color:{{ $remaining > 0 ? 'var(--positive)' : 'var(--negative)' }};font-family:var(--font-mono)">RS {{ number_format($remaining, 2) }}</p>
                            </div>
                            <div style="padding:12px;background:var(--bg-hover);border-radius:8px">
                                <p style="margin:0 0 2px;font-size:11px;color:var(--text-dim)">Daily Budget</p>
                                <p style="margin:0;font-size:18px;font-weight:700;color:var(--text);font-family:var(--font-mono)">RS {{ number_format($overall['daily_budget'] ?? 0, 2) }}</p>
                            </div>
                        </div>
                        @if ($pct >= 100)
                            <div style="margin-top:24px;display:flex;align-items:flex-end;gap:16px;">
                                <!-- Mascot with glow -->
                                <div class="owl-container" style="position:relative;width:120px;height:120px;flex-shrink:0;">
                                    <div style="position:absolute;bottom:0;width:120px;height:120px;background:radial-gradient(circle, rgba(239,68,68,0.2) 0%, rgba(239,68,68,0) 70%);border-radius:50%;z-index:-1;"></div>
                                    <video autoplay loop muted playsinline class="owl-video" style="width:100%;height:100%;object-fit:cover;object-position:bottom;">
                                        <source src="{{ asset('video/Owl_notices_spending_increase_202606250101.mp4') }}" type="video/mp4">
                                    </video>
                                </div>
                                
                                <!-- Speech bubble message -->
                                <div style="position:relative;flex:1;background:var(--bg-card);border:1px solid rgba(239,68,68,0.3);border-radius:16px;border-bottom-left-radius:4px;padding:16px;box-shadow:0 4px 12px rgba(239,68,68,0.08);">
                                    <!-- Speech bubble pointer -->
                                    <div style="position:absolute;bottom:20px;left:-6px;width:12px;height:12px;background:var(--bg-card);border-left:1px solid rgba(239,68,68,0.3);border-bottom:1px solid rgba(239,68,68,0.3);transform:rotate(45deg);"></div>
                                    
                                    <p style="margin:0 0 6px;font-size:14px;font-weight:800;color:var(--danger)">⚠️ Budget Hit!</p>
                                    <p style="margin:0;font-size:13px;color:var(--text-muted);line-height:1.5">You have exceeded your monthly budget by <strong style="color:var(--danger)">{{ number_format($pct - 100, 0) }}%</strong>.</p>
                                </div>
                            </div>
                        @endif

                        @if (!empty($budgetSummary['alerts']))
                            <div style="margin-top:24px;display:flex;align-items:flex-end;gap:16px;">
                                <!-- Mascot with glow -->
                                <div class="owl-container" style="position:relative;width:120px;height:120px;flex-shrink:0;">
                                    <div style="position:absolute;bottom:0;width:120px;height:120px;background:radial-gradient(circle, rgba(245,158,11,0.2) 0%, rgba(245,158,11,0) 70%);border-radius:50%;z-index:-1;"></div>
                                    <video autoplay loop muted playsinline class="owl-video" style="width:100%;height:100%;object-fit:cover;object-position:bottom;">
                                        <source src="{{ asset('video/Owl_notices_spending_increase_202606250101.mp4') }}" type="video/mp4">
                                    </video>
                                </div>
                                
                                <!-- Speech bubble message -->
                                <div style="position:relative;flex:1;background:var(--bg-card);border:1px solid rgba(245,158,11,0.4);border-radius:16px;border-bottom-left-radius:4px;padding:16px;box-shadow:0 4px 12px rgba(245,158,11,0.08);">
                                    <!-- Speech bubble pointer -->
                                    <div style="position:absolute;bottom:20px;left:-6px;width:12px;height:12px;background:var(--bg-card);border-left:1px solid rgba(245,158,11,0.4);border-bottom:1px solid rgba(245,158,11,0.4);transform:rotate(45deg);"></div>
                                    
                                    <p style="margin:0 0 6px;font-size:14px;font-weight:800;color:var(--warning)">⚠️ Category Budget Warning</p>
                                    @foreach ($budgetSummary['alerts'] as $alert)
                                        <p style="margin:0 0 4px;font-size:13px;color:var(--text-muted);line-height:1.5"><strong>{{ $alert['label'] }}</strong>: {{ $alert['percentage'] }}% used.</p>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="dashboard-bottom fade-in-up stagger-5" style="margin-top:24px">
        <div class="card-premium" style="overflow:hidden">
            <div class="card-body" style="padding:0">

                @php
                    $daysInMonth = now()->daysInMonth;
                    $today = now()->day;
                    $avgPerDay = $today > 0 ? $monthlyTotal / $today : 0;
                    $projectedTotal = $avgPerDay * $daysInMonth;
                    $monthChange = $previousMonthTotal > 0 ? round(($monthlyTotal - $previousMonthTotal) / $previousMonthTotal * 100, 1) : 0;
                @endphp

                {{-- Header: Title + Trend + Range Switcher --}}
                <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;padding:20px 24px 0">
                    <div style="display:flex;align-items:center;gap:14px">
                        <div style="width:42px;height:42px;border-radius:12px;background:rgba(14,207,179,0.12);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i data-lucide="trending-up" style="width:20px;height:20px;color:var(--primary)"></i>
                        </div>
                        <div>
                            <h5 style="margin:0;font-size:17px;font-weight:700;letter-spacing:-0.02em;color:var(--text)">{{ now()->format('F Y') }} <span style="font-weight:400;color:var(--text-dim)">Analytics</span></h5>
                            <div style="display:flex;align-items:center;gap:6px;margin-top:2px">
                                <span style="font-size:12px;font-weight:600;color:{{ $monthChange >= 0 ? 'var(--success)' : 'var(--danger)' }}">{{ $monthChange >= 0 ? '▲' : '▼' }} {{ number_format(abs($monthChange), 1) }}%</span>
                                <span style="font-size:11px;color:var(--text-dim)">vs last month</span>
                            </div>
                        </div>
                    </div>
                    <div class="range-switcher" style="display:flex;gap:4px;background:var(--bg-hover);border-radius:10px;padding:3px">
                        @foreach (['7d' => '7D', '30d' => '30D', '3m' => '3M', '6m' => '6M', '1y' => '1Y'] as $rk => $rl)
                            <button class="range-btn" data-range="{{ $rk }}" style="padding:6px 14px;border:none;border-radius:7px;background:{{ $rk === '30d' ? '#0ECFB3' : 'transparent' }};color:{{ $rk === '30d' ? '#050809' : 'var(--text-dim)' }};font-size:12px;font-weight:600;cursor:pointer;transition:all 150ms;font-family:var(--font-sans)">{{ $rl }}</button>
                        @endforeach
                    </div>
                </div>

                {{-- KPI cards --}}
                <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:0;margin:16px 24px 0;border:1px solid var(--border);border-radius:12px;overflow:hidden">
                    <div style="padding:14px 16px;border-right:1px solid var(--border-light)">
                        <p style="margin:0 0 2px;font-size:11px;color:var(--text-dim);font-weight:500;letter-spacing:0.3px">TOTAL SPENT</p>
                        <p style="margin:0;font-size:19px;font-weight:800;color:var(--success);font-variant-numeric:tabular-nums" id="kpiTotal">RS {{ number_format($monthlyTotal, 2) }}</p>
                    </div>
                    <div style="padding:14px 16px;border-right:1px solid var(--border-light)">
                        <p style="margin:0 0 2px;font-size:11px;color:var(--text-dim);font-weight:500;letter-spacing:0.3px">DAILY AVG</p>
                        <p style="margin:0;font-size:19px;font-weight:800;color:var(--secondary);font-variant-numeric:tabular-nums" id="kpiAvg">RS {{ number_format($avgPerDay, 2) }}</p>
                    </div>
                    <div style="padding:14px 16px;border-right:1px solid var(--border-light)">
                        <p style="margin:0 0 2px;font-size:11px;color:var(--text-dim);font-weight:500;letter-spacing:0.3px">PROJECTED</p>
                        <p style="margin:0;font-size:19px;font-weight:800;color:var(--warning);font-variant-numeric:tabular-nums" id="kpiProjected">RS {{ number_format($projectedTotal, 2) }}</p>
                    </div>
                    <div style="padding:14px 16px">
                        <p style="margin:0 0 2px;font-size:11px;color:var(--text-dim);font-weight:500;letter-spacing:0.3px">BUDGET USED</p>
                        <p style="margin:0;font-size:19px;font-weight:800;color:var(--accent);font-variant-numeric:tabular-nums" id="kpiBudget">{{ $today > 0 ? number_format(($today / $daysInMonth) * 100, 0) : 0 }}%</p>
                    </div>
                </div>

                {{-- Chart --}}
                <div style="padding:20px 24px 8px;position:relative">
                    <div style="height:280px;position:relative">
                        <canvas id="spendingChart"></canvas>
                    </div>
                </div>

                {{-- Footer stats --}}
                <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:0;margin:0 24px 20px;border:1px solid var(--border);border-radius:12px;overflow:hidden">
                    <div style="padding:12px 16px;border-right:1px solid var(--border-light)">
                        <p style="margin:0 0 2px;font-size:10px;color:var(--text-dim);font-weight:500;text-transform:uppercase;letter-spacing:0.5px">Highest Day</p>
                        <p style="margin:0;font-size:14px;font-weight:700;color:var(--text);font-variant-numeric:tabular-nums" id="statHighest">—</p>
                    </div>
                    <div style="padding:12px 16px;border-right:1px solid var(--border-light)">
                        <p style="margin:0 0 2px;font-size:10px;color:var(--text-dim);font-weight:500;text-transform:uppercase;letter-spacing:0.5px">Lowest Day</p>
                        <p style="margin:0;font-size:14px;font-weight:700;color:var(--text);font-variant-numeric:tabular-nums" id="statLowest">—</p>
                    </div>
                    <div style="padding:12px 16px;border-right:1px solid var(--border-light)">
                        <p style="margin:0 0 2px;font-size:10px;color:var(--text-dim);font-weight:500;text-transform:uppercase;letter-spacing:0.5px">Daily Avg</p>
                        <p style="margin:0;font-size:14px;font-weight:700;color:var(--text);font-variant-numeric:tabular-nums" id="statAvg">—</p>
                    </div>
                    <div style="padding:12px 16px">
                        <p style="margin:0 0 2px;font-size:10px;color:var(--text-dim);font-weight:500;text-transform:uppercase;letter-spacing:0.5px">Transactions</p>
                        <p style="margin:0;font-size:14px;font-weight:700;color:var(--text);font-variant-numeric:tabular-nums" id="statCount">—</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        Chart.defaults.color = '#6B7280';
        Chart.defaults.borderColor = '#E7ECF2';

        // ── Monthly bar chart ──
        const monthlyData = @json($monthlySpending);
        const categoryData = @json($categoryDistribution);

        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const monthlyValues = new Array(12).fill(0);
        monthlyData.forEach(function (d) { monthlyValues[d.month - 1] = parseFloat(d.total); });

        const barCtx = document.getElementById('monthlyChart').getContext('2d');
        const barGradient = barCtx.createLinearGradient(0, 0, 0, 280);
        barGradient.addColorStop(0, 'rgba(22, 199, 183, 0.65)');
        barGradient.addColorStop(1, 'rgba(22, 199, 183, 0.05)');

        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'Spending',
                    data: monthlyValues,
                    backgroundColor: barGradient,
                    borderColor: '#16C7B7',
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
                        backgroundColor: '#FFFFFF',
                        titleColor: '#1F2937',
                        bodyColor: '#6B7280',
                        borderColor: '#E7ECF2',
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
                        grid: { color: 'rgba(231, 236, 242, 0.6)' },
                        ticks: { callback: function (v) { return '$' + v; } }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });

        // ── Category doughnut chart ──
        const colors = ['#16C7B7', '#3B82F6', '#22C55E', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#14B8A6', '#F97316'];

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
                                color: '#6B7280',
                                boxWidth: 8,
                                padding: 8,
                                font: { size: 11 }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#FFFFFF',
                            titleColor: '#1F2937',
                            bodyColor: '#6B7280',
                            borderColor: '#E7ECF2',
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

        // ── Spending line chart (trading-style) ──
        const chartRanges = @json($chartRanges);
        let activeRange = '30d';
        let spendingChart = null;
        const chartEl = document.getElementById('spendingChart');
        if (!chartEl) return;

        function fmtDate(d) {
            var p = d.split('-');
            var dt = new Date(+p[0], +p[1] - 1, +p[2]);
            return dt.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        }

        function renderChart(rangeKey) {
            var range = chartRanges[rangeKey];
            if (!range) return;
            var data = range.dailyData || [];

            var labels = data.map(function (d) { return fmtDate(d.date); });
            var values = data.map(function (d) { return d.total; });

            var ctx = chartEl.getContext('2d');
            var grad = ctx.createLinearGradient(0, 0, 0, 280);
            grad.addColorStop(0, 'rgba(22, 199, 183, 0.3)');
            grad.addColorStop(1, 'rgba(22, 199, 183, 0.0)');

            if (spendingChart) { spendingChart.destroy(); }

            spendingChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Spending',
                        data: values,
                        borderColor: '#16C7B7',
                        backgroundColor: grad,
                        borderWidth: 2.5,
                        pointRadius: 0,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: '#16C7B7',
                        pointHoverBorderColor: '#FFFFFF',
                        pointHoverBorderWidth: 2,
                        tension: 0.35,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 800,
                        easing: 'easeInOutQuart'
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#FFFFFF',
                            titleColor: '#1F2937',
                            bodyColor: '#16C7B7',
                            borderColor: 'rgba(22,199,183,0.2)',
                            borderWidth: 1,
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                title: function (items) {
                                    return items[0].label;
                                },
                                label: function (ctx) {
                                    return '$' + parseFloat(ctx.raw).toLocaleString('en-US', { minimumFractionDigits: 2 });
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#E7ECF2',
                                drawBorder: false,
                            },
                            ticks: {
                                callback: function (v) { return '$' + v; },
                                font: { size: 10 },
                                color: '#6B7280',
                                maxTicksLimit: 6,
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { size: 10 },
                                color: '#6B7280',
                                maxTicksLimit: 12,
                                maxRotation: 0,
                            }
                        }
                    }
                }
            });

            // Update KPI cards
            document.getElementById('kpiTotal').textContent = '$' + Number(range.total).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('kpiAvg').textContent = '$' + Number(range.avgDaily).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('kpiProjected').textContent = '$' + Number(range.total).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('kpiBudget').textContent = data.filter(function (d) { return d.total > 0; }).length + '/' + data.length + ' days';

            // Update footer stats
            var h = range.highestDay;
            document.getElementById('statHighest').textContent = h ? '$' + Number(h.total).toLocaleString('en-US', { minimumFractionDigits: 2 }) + ' · ' + fmtDate(h.date) : '—';
            var l = range.lowestDay;
            document.getElementById('statLowest').textContent = l ? '$' + Number(l.total).toLocaleString('en-US', { minimumFractionDigits: 2 }) + ' · ' + fmtDate(l.date) : '—';
            document.getElementById('statAvg').textContent = '$' + Number(range.avgDaily).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('statCount').textContent = Number(range.count).toLocaleString();
        }

        // Range switcher click handlers
        document.querySelectorAll('.range-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var rk = this.getAttribute('data-range');
                if (!rk || rk === activeRange) return;
                activeRange = rk;
                document.querySelectorAll('.range-btn').forEach(function (b) {
                    b.style.background = 'transparent';
                    b.style.color = 'var(--text-muted)';
                });
                this.style.background = '#16C7B7';
                this.style.color = '#FFFFFF';
                renderChart(rk);
            });
        });

        // Initial render (30d default)
        renderChart('30d');
    });
    </script>
    @endpush
</x-app-layout>
