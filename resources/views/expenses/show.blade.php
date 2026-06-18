<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Expense Details</h4>
            <p class="text-muted small mb-0">{{ $expense->expense_date->format('l, F j, Y') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    @if ($expense->ai_confidence === null)
        <div class="alert alert-info border-0 shadow-sm d-flex align-items-center gap-3 mb-4" id="aiPendingAlert">
            <div class="spinner-border text-primary" role="status" style="width: 1.25rem; height: 1.25rem;"></div>
            <div>
                <strong>AI is analyzing your expense...</strong>
                <p class="mb-0 small">Category, merchant, and recurring status will be detected automatically.</p>
            </div>
        </div>
    @elseif ($expense->ai_confidence < 0.5)
        <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center gap-3 mb-4">
            <i class="bi bi-exclamation-triangle fs-4 text-warning"></i>
            <div>
                <strong>Low AI Confidence</strong>
                <p class="mb-0 small">AI couldn't confidently categorize this expense. <a href="{{ route('expenses.edit', $expense) }}">Edit it manually</a>.</p>
            </div>
        </div>
    @else
        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center gap-3 mb-4">
            <i class="bi bi-check-circle-fill fs-4 text-success"></i>
            <div>
                <strong>AI Categorized</strong>
                <p class="mb-0 small">Detected as <strong>{{ $expense->category }}</strong>
                    @if ($expense->merchant) from <strong>{{ $expense->merchant }}</strong> @endif
                    with {{ number_format($expense->ai_confidence * 100, 0) }}% confidence.</p>
            </div>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h1 class="display-5 fw-bold mb-1">${{ number_format($expense->amount, 2) }}</h1>
                            <p class="text-muted mb-0">
                                <i class="bi bi-calendar me-1"></i> {{ $expense->expense_date->format('F d, Y') }}
                                <span class="mx-2">|</span>
                                <i class="bi bi-credit-card me-1"></i> {{ $expense->payment_method }}
                            </p>
                        </div>
                        <div>
                            @if ($expense->ai_confidence !== null)
                                <span class="badge bg-{{ $expense->category !== 'Other' ? 'success' : 'secondary' }} fs-6 px-3 py-2">
                                    @switch($expense->category)
                                        @case('Food & Dining') 🍔 @break
                                        @case('Shopping') 🛍️ @break
                                        @case('Transport') 🚗 @break
                                        @case('Fuel') ⛽ @break
                                        @case('Groceries') 🛒 @break
                                        @case('Bills') 📄 @break
                                        @case('Utilities') 💡 @break
                                        @case('Healthcare') 🏥 @break
                                        @case('Education') 📚 @break
                                        @case('Entertainment') 🎬 @break
                                        @case('Travel') ✈️ @break
                                        @case('Rent') 🏠 @break
                                        @case('Investment') 📈 @break
                                        @case('Salary') 💰 @break
                                        @case('Subscription') 🔄 @break
                                        @default 🤖
                                    @endswitch
                                    {{ $expense->category }}
                                </span>
                            @else
                                <span class="badge bg-info fs-6 px-3 py-2">
                                    <i class="bi bi-robot me-1"></i> Analyzing...
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-semibold text-muted small text-uppercase mb-2">Description</h6>
                        <p class="fs-5 mb-0">{{ $expense->description }}</p>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="bg-light rounded-3 p-3">
                                <small class="text-muted d-block text-uppercase small">Merchant</small>
                                <strong class="fs-6">
                                    @if ($expense->merchant)
                                        {{ $expense->merchant }}
                                    @elseif ($expense->ai_confidence === null)
                                        <span class="text-info"><i class="bi bi-robot me-1"></i> Detecting...</span>
                                    @else
                                        <span class="text-muted">Not detected</span>
                                    @endif
                                </strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light rounded-3 p-3">
                                <small class="text-muted d-block text-uppercase small">Payment Method</small>
                                <strong class="fs-6">{{ $expense->payment_method }}</strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light rounded-3 p-3">
                                <small class="text-muted d-block text-uppercase small">Recurring</small>
                                <strong class="fs-6">
                                    @if ($expense->ai_confidence !== null)
                                        @if ($expense->is_recurring)
                                            <span class="text-primary"><i class="bi bi-arrow-repeat"></i> Yes</span>
                                        @else
                                            No
                                        @endif
                                    @else
                                        <span class="text-info"><i class="bi bi-robot me-1"></i> Detecting...</span>
                                    @endif
                                </strong>
                            </div>
                        </div>
                    </div>

                    @if ($expense->notes)
                        <div>
                            <h6 class="fw-semibold text-muted small text-uppercase mb-2">Notes</h6>
                            <p class="mb-0">{{ $expense->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-robot me-1"></i> AI Classification</h6>
                </div>
                <div class="card-body">
                    @if ($expense->ai_confidence !== null)
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Confidence Score</small>
                            <div class="progress" style="height: 24px;">
                                <div class="progress-bar rounded-2 {{ $expense->ai_confidence >= 0.8 ? 'bg-success' : ($expense->ai_confidence >= 0.5 ? 'bg-warning text-dark' : 'bg-danger') }}"
                                     role="progressbar"
                                     style="width: {{ $expense->ai_confidence * 100 }}%"
                                     aria-valuenow="{{ $expense->ai_confidence * 100 }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ number_format($expense->ai_confidence * 100, 0) }}%
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                            <small class="text-muted">Category</small>
                            <strong>{{ $expense->category }}</strong>
                        </div>
                        <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                            <small class="text-muted">Merchant</small>
                            <strong>{{ $expense->merchant ?? 'N/A' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                            <small class="text-muted">Recurring</small>
                            <strong>{{ $expense->is_recurring ? 'Yes' : 'No' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">Detected By</small>
                            <strong><i class="bi bi-robot text-primary"></i> AI</strong>
                        </div>
                        @if ($expense->ai_confidence < 0.5)
                            <div class="alert alert-warning py-2 px-3 mt-3 mb-0 small">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                AI confidence is low. <a href="{{ route('expenses.edit', $expense) }}">Review manually</a>.
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4" id="aiLoadingState">
                            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
                            <p class="fw-semibold mb-1">AI Analyzing</p>
                            <p class="text-muted small mb-0">Processing your expense...</p>
                            <p class="text-muted small mb-0">This should take just a moment.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-gear me-1"></i> Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil"></i> Edit Expense
                        </a>
                        <form method="POST" action="{{ route('expenses.destroy', $expense) }}" onsubmit="return confirm('Delete this expense permanently?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-trash"></i> Delete Expense
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@if ($expense->ai_confidence === null)
<script>setTimeout(function(){ location.reload(true); }, 3000);</script>
@endif
