<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Dashboard</h4>
            <p class="text-muted mb-0 small">{{ now()->format('l, F j, Y') }}</p>
        </div>
        <a href="{{ route('expenses.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add Expense
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 p-2 rounded">
                            <i class="bi bi-calendar-day fs-4 text-primary"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-muted small mb-0">Today</p>
                            <h4 class="fw-bold mb-0 text-primary">${{ number_format($todayTotal, 2) }}</h4>
                            <small class="text-muted">{{ $todayCount }} transaction{{ $todayCount !== 1 ? 's' : '' }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-success bg-opacity-10 p-2 rounded">
                            <i class="bi bi-graph-up fs-4 text-success"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-muted small mb-0">This Month</p>
                            <h4 class="fw-bold mb-0 text-success">${{ number_format($monthlyTotal, 2) }}</h4>
                            <small class="text-muted">{{ $monthlyCount }} transaction{{ $monthlyCount !== 1 ? 's' : '' }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-warning bg-opacity-10 p-2 rounded">
                            <i class="bi bi-currency-dollar fs-4 text-warning"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-muted small mb-0">This Year</p>
                            <h4 class="fw-bold mb-0 text-warning">${{ number_format($yearlyTotal, 2) }}</h4>
                            <small class="text-muted">{{ $yearlyCount }} transaction{{ $yearlyCount !== 1 ? 's' : '' }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-danger bg-opacity-10 p-2 rounded">
                            <i class="bi bi-arrow-up-circle fs-4 text-danger"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-muted small mb-0">Highest</p>
                            <h4 class="fw-bold mb-0 text-danger">${{ $highestExpense ? number_format($highestExpense->amount, 2) : '0.00' }}</h4>
                            <small class="text-muted">{{ $topCategory ? $topCategory : 'N/A' }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-bar-chart-line me-1"></i> Monthly Spending</h6>
                    <span class="badge bg-primary">{{ now()->year }}</span>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="220"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-pie-chart me-1"></i> Categories</h6>
                    <span class="badge bg-secondary">{{ $categoryDistribution->count() }}</span>
                </div>
                <div class="card-body d-flex align-items-center">
                    <canvas id="categoryChart" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-trophy me-1"></i> Top Categories</h6>
                </div>
                <div class="card-body">
                    @if ($categoryDistribution->isNotEmpty())
                        @php $grandTotal = $categoryDistribution->sum('total'); @endphp
                        @foreach ($categoryDistribution->take(8) as $cat)
                            @php $pct = $grandTotal > 0 ? ($cat->total / $grandTotal) * 100 : 0; @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small fw-medium">{{ $cat->category }}</span>
                                    <span class="small text-muted">${{ number_format($cat->total, 2) }} ({{ number_format($pct, 1) }}%)</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-{{ ['primary', 'success', 'warning', 'danger', 'info', 'dark', 'secondary', 'primary'][$loop->index % 8] }}"
                                         role="progressbar"
                                         style="width: {{ $pct }}%"
                                         aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        @if ($categoryDistribution->count() > 8)
                            <div class="text-center mt-2">
                                <small class="text-muted">+{{ $categoryDistribution->count() - 8 }} more categories</small>
                            </div>
                        @endif
                    @else
                        <p class="text-muted small text-center py-4 mb-0">No expenses yet</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-calendar3 me-1"></i> {{ now()->format('F') }} Summary</h6>
                </div>
                <div class="card-body">
                    @php
                        $daysInMonth = now()->daysInMonth;
                        $today = now()->day;
                        $avgPerDay = $today > 0 ? $monthlyTotal / $today : 0;
                        $projectedTotal = $avgPerDay * $daysInMonth;
                    @endphp
                    <div class="row g-3 mb-3">
                        <div class="col-4 text-center border-end">
                            <p class="text-muted small mb-0">Monthly Total</p>
                            <h5 class="fw-bold mb-0 text-success">${{ number_format($monthlyTotal, 2) }}</h5>
                        </div>
                        <div class="col-4 text-center border-end">
                            <p class="text-muted small mb-0">Daily Avg</p>
                            <h5 class="fw-bold mb-0 text-info">${{ number_format($avgPerDay, 2) }}</h5>
                        </div>
                        <div class="col-4 text-center">
                            <p class="text-muted small mb-0">Projected</p>
                            <h5 class="fw-bold mb-0 text-warning">${{ number_format($projectedTotal, 2) }}</h5>
                        </div>
                    </div>

                    @if ($dailyBreakdown->isNotEmpty())
                        <div class="mt-3">
                            <div class="d-flex justify-content-between small text-muted mb-1">
                                <span>Daily spending this month</span>
                                <span>Week {{ (int) ceil($today / 7) }} of {{ (int) ceil($daysInMonth / 7) }}</span>
                            </div>
                            <div class="d-flex align-items-end gap-1" style="height: 60px;">
                                @php
                                    $maxDaily = $dailyBreakdown->max('total') ?: 1;
                                @endphp
                                @foreach ($dailyBreakdown as $day)
                                    @php
                                        $h = max(4, ($day->total / $maxDaily) * 56);
                                        $isToday = $day->date == today()->toDateString();
                                    @endphp
                                    <div class="flex-grow-1 text-center" title="{{ \Carbon\Carbon::parse($day->date)->format('M d') }}: ${{ number_format($day->total, 2) }}">
                                        <div class="rounded-1 mx-auto {{ $isToday ? 'bg-primary' : 'bg-primary bg-opacity-25' }}"
                                             style="height: {{ $h }}px; width: 100%;"></div>
                                        <small class="text-muted" style="font-size: 9px;">
                                            {{ \Carbon\Carbon::parse($day->date)->format('d') }}
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <p class="text-muted small text-center py-4 mb-0">No expenses this month</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
            <h6 class="fw-semibold mb-0"><i class="bi bi-clock-history me-1"></i> Recent Expenses</h6>
            <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        <div class="card-body p-0">
            @if ($recentExpenses->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Category</th>
                                <th>Merchant</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentExpenses as $expense)
                                <tr>
                                    <td class="text-nowrap small">{{ $expense->expense_date->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('expenses.show', $expense) }}" class="text-decoration-none fw-medium">
                                            {{ Str::limit($expense->description, 45) }}
                                        </a>
                                    </td>
                                    <td><span class="badge bg-secondary bg-opacity-75">{{ $expense->category }}</span></td>
                                    <td class="small">{{ $expense->merchant ?? '-' }}</td>
                                    <td class="text-end fw-semibold">${{ number_format($expense->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                    <p class="mb-2">No expenses recorded yet</p>
                    <a href="{{ route('expenses.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg"></i> Add Your First Expense
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const monthlyData = @json($monthlySpending);
    const categoryData = @json($categoryDistribution);

    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const monthlyValues = new Array(12).fill(0);
    monthlyData.forEach(function (d) {
        monthlyValues[d.month - 1] = parseFloat(d.total);
    });

    const barCtx = document.getElementById('monthlyChart').getContext('2d');
    const gradient = barCtx.createLinearGradient(0, 0, 0, 220);
    gradient.addColorStop(0, 'rgba(13, 110, 253, 0.7)');
    gradient.addColorStop(1, 'rgba(13, 110, 253, 0.1)');

    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Spending',
                data: monthlyValues,
                backgroundColor: gradient,
                borderColor: '#0d6efd',
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
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
                    ticks: {
                        callback: function (v) { return '$' + v; }
                    }
                }
            }
        }
    });

    const colors = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14', '#20c997', '#0dcaf0', '#d63384', '#6610f2', '#f5365c', '#2dce89', '#fb6340', '#172b4d', '#5e72e4', '#8898aa'];

    if (categoryData.length > 0) {
        new Chart(document.getElementById('categoryChart'), {
            type: 'doughnut',
            data: {
                labels: categoryData.map(function (d) {
                    return d.category + ' (' + new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', minimumFractionDigits: 0 }).format(d.total) + ')';
                }),
                datasets: [{
                    data: categoryData.map(function (d) { return parseFloat(d.total); }),
                    backgroundColor: colors.slice(0, categoryData.length),
                    borderWidth: 2,
                    borderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, padding: 6, font: { size: 10 } }
                    },
                    tooltip: {
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
        document.getElementById('categoryChart').parentElement.innerHTML = '<p class="text-muted small text-center w-100 mb-0">No data</p>';
    }
});
</script>
@endpush
