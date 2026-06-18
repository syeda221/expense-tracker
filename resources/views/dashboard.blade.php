<x-app-layout>
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-primary border-2 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle text-muted mb-1">Today's Expenses</h6>
                            <h3 class="card-title mb-0 text-primary">${{ number_format($todayTotal, 2) }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-calendar-day fs-3 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success border-2 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle text-muted mb-1">This Month</h6>
                            <h3 class="card-title mb-0 text-success">${{ number_format($monthlyTotal, 2) }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-graph-up fs-3 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning border-2 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle text-muted mb-1">This Year</h6>
                            <h3 class="card-title mb-0 text-warning">${{ number_format($yearlyTotal, 2) }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-currency-dollar fs-3 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger border-2 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle text-muted mb-1">Highest Expense</h6>
                            <h3 class="card-title mb-0 text-danger">
                                ${{ $highestExpense ? number_format($highestExpense->amount, 2) : '0.00' }}
                            </h3>
                            @if ($topCategory)
                                <small class="text-muted">Top: {{ $topCategory }}</small>
                            @endif
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="bi bi-arrow-up-circle fs-3 text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Monthly Spending</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Category Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Recent Expenses</h5>
            <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        <div class="card-body p-0">
            @if ($recentExpenses->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Category</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentExpenses as $expense)
                                <tr>
                                    <td class="text-nowrap">{{ $expense->expense_date->format('M d') }}</td>
                                    <td>
                                        <a href="{{ route('expenses.show', $expense) }}" class="text-decoration-none">
                                            {{ Str::limit($expense->description, 50) }}
                                        </a>
                                    </td>
                                    <td><span class="badge bg-secondary">{{ $expense->category }}</span></td>
                                    <td class="text-end fw-semibold">${{ number_format($expense->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                    No expenses yet.
                    <a href="{{ route('expenses.create') }}" class="d-block mt-2">Add your first expense</a>
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

    new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Spending',
                data: monthlyValues,
                backgroundColor: 'rgba(13, 110, 253, 0.6)',
                borderColor: 'rgba(13, 110, 253, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    const colors = [
        '#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1',
        '#fd7e14', '#20c997', '#d63384', '#0dcaf0', '#6610f2',
        '#f5365c', '#2dce89', '#fb6340', '#172b4d', '#5e72e4', '#8898aa'
    ];

    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: categoryData.map(d => d.category),
            datasets: [{
                data: categoryData.map(d => parseFloat(d.total)),
                backgroundColor: colors.slice(0, categoryData.length),
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: { boxWidth: 12, padding: 8, font: { size: 11 } }
                }
            }
        }
    });
});
</script>
@endpush
