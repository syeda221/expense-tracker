<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Expense Details</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h2 class="mb-1">${{ number_format($expense->amount, 2) }}</h2>
                            <p class="text-muted mb-0">{{ $expense->expense_date->format('F d, Y') }}</p>
                        </div>
                        <span class="badge bg-secondary fs-6">{{ $expense->category }}</span>
                    </div>

                    <h6 class="fw-bold">Description</h6>
                    <p class="mb-4">{{ $expense->description }}</p>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <small class="text-muted d-block">Merchant</small>
                            <strong>{{ $expense->merchant ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Payment Method</small>
                            <strong>{{ $expense->payment_method }}</strong>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Recurring</small>
                            <strong>
                                @if ($expense->is_recurring)
                                    <span class="text-primary"><i class="bi bi-arrow-repeat"></i> Yes</span>
                                @else
                                    No
                                @endif
                            </strong>
                        </div>
                    </div>

                    @if ($expense->notes)
                        <h6 class="fw-bold">Notes</h6>
                        <p class="mb-0">{{ $expense->notes }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">AI Classification</h6>
                </div>
                <div class="card-body">
                    @if ($expense->ai_confidence !== null)
                        <div class="mb-3">
                            <small class="text-muted d-block">Confidence Score</small>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar {{ $expense->ai_confidence >= 0.8 ? 'bg-success' : ($expense->ai_confidence >= 0.5 ? 'bg-warning' : 'bg-danger') }}"
                                     role="progressbar"
                                     style="width: {{ $expense->ai_confidence * 100 }}%"
                                     aria-valuenow="{{ $expense->ai_confidence * 100 }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ number_format($expense->ai_confidence * 100, 0) }}%
                                </div>
                            </div>
                        </div>
                        <p class="mb-1"><small class="text-muted">Recurring:</small> {{ $expense->is_recurring ? 'Yes' : 'No' }}</p>
                    @else
                        <p class="text-muted small mb-0">
                            <i class="bi bi-clock-history"></i> AI classification pending.
                        </p>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-pencil"></i> Edit Expense
                        </a>
                        <form method="POST" action="{{ route('expenses.destroy', $expense) }}" onsubmit="return confirm('Delete this expense permanently?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                <i class="bi bi-trash"></i> Delete Expense
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
