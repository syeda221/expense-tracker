<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Expenses</h4>
        <a href="{{ route('expenses.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add Expense
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('expenses.index') }}" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search description or merchant..." value="{{ $filters['search'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <input type="text" name="category" class="form-control" placeholder="Category" value="{{ $filters['category'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <input type="text" name="payment_method" class="form-control" placeholder="Payment method" value="{{ $filters['payment_method'] ?? '' }}">
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

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Merchant</th>
                            <th>Payment</th>
                            <th class="text-end">Amount</th>
                            <th class="text-center">Recurring</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($expenses as $expense)
                            <tr>
                                <td class="text-nowrap">{{ $expense->expense_date->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('expenses.show', $expense) }}" class="text-decoration-none fw-medium">
                                        {{ Str::limit($expense->description, 40) }}
                                    </a>
                                </td>
                                <td><span class="badge bg-secondary">{{ $expense->category }}</span></td>
                                <td>{{ $expense->merchant ?? '-' }}</td>
                                <td>{{ $expense->payment_method }}</td>
                                <td class="text-end fw-semibold">${{ number_format($expense->amount, 2) }}</td>
                                <td class="text-center">
                                    @if ($expense->is_recurring)
                                        <i class="bi bi-arrow-repeat text-primary"></i>
                                    @else
                                        <span class="text-muted">-</span>
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
                                    No expenses found.
                                    <a href="{{ route('expenses.create') }}" class="d-block mt-2">Add your first expense</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($expenses->hasPages())
            <div class="card-footer">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
