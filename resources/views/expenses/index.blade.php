<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Expenses</h4>
            <p class="text-muted small mb-0">{{ $expenses->total() }} total expenses</p>
        </div>
        <a href="{{ route('expenses.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add Expense
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('expenses.index') }}" class="row g-2">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search description or merchant..." value="{{ $filters['search'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <input type="text" name="category" class="form-control" placeholder="Category" value="{{ $filters['category'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <input type="text" name="payment_method" class="form-control" placeholder="Payment" value="{{ $filters['payment_method'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}" placeholder="From">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}" placeholder="To">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Merchant</th>
                            <th>Payment</th>
                            <th class="text-end">Amount</th>
                            <th class="text-center">AI</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($expenses as $expense)
                            <tr>
                                <td class="text-nowrap small">{{ $expense->expense_date->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('expenses.show', $expense) }}" class="text-decoration-none fw-medium">
                                        {{ Str::limit($expense->description, 40) }}
                                    </a>
                                </td>
                                <td>
                                    @if ($expense->ai_confidence !== null)
                                        <span class="badge bg-{{ $expense->category !== 'Other' ? 'success' : 'secondary' }} bg-opacity-75">
                                            {{ $expense->category }}
                                        </span>
                                    @else
                                        <span class="badge bg-info bg-opacity-50">
                                            <i class="bi bi-robot me-1"></i> Analyzing
                                        </span>
                                    @endif
                                </td>
                                <td class="small">{{ $expense->merchant ?? '-' }}</td>
                                <td class="small">{{ $expense->payment_method }}</td>
                                <td class="text-end fw-semibold">${{ number_format($expense->amount, 2) }}</td>
                                <td class="text-center">
                                    @if ($expense->ai_confidence !== null)
                                        @if ($expense->ai_confidence >= 0.8)
                                            <span class="badge bg-success bg-opacity-10 text-success" title="AI Confidence: {{ number_format($expense->ai_confidence * 100, 0) }}%">
                                                <i class="bi bi-check-circle-fill"></i>
                                            </span>
                                        @elseif ($expense->ai_confidence >= 0.5)
                                            <span class="badge bg-warning bg-opacity-10 text-warning" title="AI Confidence: {{ number_format($expense->ai_confidence * 100, 0) }}%">
                                                <i class="bi bi-exclamation-circle-fill"></i>
                                            </span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger" title="AI Confidence: {{ number_format($expense->ai_confidence * 100, 0) }}%">
                                                <i class="bi bi-x-circle-fill"></i>
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge bg-info bg-opacity-10 text-info" title="AI analysis pending">
                                            <i class="bi bi-hourglass-split"></i>
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('expenses.destroy', $expense) }}" class="d-inline" onsubmit="return confirm('Delete this expense?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    <p class="mb-2">No expenses found.</p>
                                    <a href="{{ route('expenses.create') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-lg"></i> Add your first expense
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($expenses->hasPages())
            <div class="card-footer bg-white border-top">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>
@push('scripts')
<script>
(function() {
    var hasAnalyzing = document.querySelector('.badge.bg-info');
    if (hasAnalyzing) {
        setTimeout(function() { location.reload(true); }, 3000);
    }
})();
</script>
@endpush
</x-app-layout>
