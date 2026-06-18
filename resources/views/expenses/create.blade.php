<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Add Expense</h4>
            <p class="text-muted small mb-0">AI will auto-categorize your expense after saving</p>
        </div>
        <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('expenses.store') }}" id="expenseForm">
                @csrf

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-semibold mb-0"><i class="bi bi-receipt me-1"></i> Expense Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="amount" class="form-label fw-medium">Amount <span class="text-danger">*</span></label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text">$</span>
                                    <input id="amount" type="number" step="0.01" min="0" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount') }}" placeholder="0.00" required>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="expense_date" class="form-label fw-medium">Date <span class="text-danger">*</span></label>
                                <input id="expense_date" type="date" class="form-control form-control-lg @error('expense_date') is-invalid @enderror" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required>
                                @error('expense_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-medium">Description <span class="text-danger">*</span></label>
                            <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" rows="3" placeholder="e.g. Lunch at McDonald's, Uber ride to downtown, Monthly electricity bill..." required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">A detailed description helps AI categorize accurately.</div>
                        </div>

                        <div class="mb-3">
                            <label for="payment_method" class="form-label fw-medium">Payment Method <span class="text-danger">*</span></label>
                            <select id="payment_method" class="form-select @error('payment_method') is-invalid @enderror" name="payment_method" required>
                                <option value="">Select payment method</option>
                                <option value="Cash" @selected(old('payment_method') == 'Cash')>Cash</option>
                                <option value="Credit Card" @selected(old('payment_method') == 'Credit Card')>Credit Card</option>
                                <option value="Debit Card" @selected(old('payment_method') == 'Debit Card')>Debit Card</option>
                                <option value="UPI" @selected(old('payment_method') == 'UPI')>UPI</option>
                                <option value="Bank Transfer" @selected(old('payment_method') == 'Bank Transfer')>Bank Transfer</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-semibold mb-0"><i class="bi bi-robot me-1"></i> AI Detection</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Category</label>
                                <div class="bg-light rounded-3 p-3 border">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-primary rounded-pill px-2 py-1">🤖</span>
                                        <span class="fw-semibold small">Auto Detect (AI)</span>
                                    </div>
                                    <p class="text-muted small mb-0">AI will automatically detect after saving.</p>
                                </div>
                                <input type="hidden" name="category" value="Other">
                            </div>
                            <div class="col-md-6">
                                <label for="merchant" class="form-label fw-medium">Merchant</label>
                                <input id="merchant" type="text" class="form-control @error('merchant') is-invalid @enderror" name="merchant" value="{{ old('merchant') }}" placeholder="Leave blank to let AI detect the merchant.">
                                @error('merchant')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Optional. AI can detect it automatically.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-semibold mb-0"><i class="bi bi-journal-text me-1"></i> Additional Notes</h6>
                    </div>
                    <div class="card-body">
                        <label for="notes" class="form-label fw-medium">Notes</label>
                        <textarea id="notes" class="form-control @error('notes') is-invalid @enderror" name="notes" rows="3" placeholder="Any additional information...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-lg px-4" id="saveBtn">
                        <i class="bi bi-check-lg"></i> Save Expense
                    </button>
                    <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary btn-lg">Cancel</a>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center py-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                        <i class="bi bi-robot fs-2 text-primary"></i>
                    </div>
                    <h5 class="fw-bold">AI Assistant</h5>
                    <p class="text-muted small mb-0">Your intelligent expense manager</p>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item small">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Only amount and description are required.
                    </li>
                    <li class="list-group-item small">
                        <i class="bi bi-magic text-primary me-2"></i>
                        AI will automatically categorize your expense.
                    </li>
                    <li class="list-group-item small">
                        <i class="bi bi-shop text-info me-2"></i>
                        Merchant names are detected automatically.
                    </li>
                    <li class="list-group-item small">
                        <i class="bi bi-arrow-repeat text-warning me-2"></i>
                        Recurring expenses are identified automatically.
                    </li>
                    <li class="list-group-item small">
                        <i class="bi bi-pencil-square text-secondary me-2"></i>
                        You can edit AI suggestions anytime.
                    </li>
                    <li class="list-group-item small">
                        <i class="bi bi-camera text-muted me-2"></i>
                        Receipt scanning will be available in a future update.
                    </li>
                </ul>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3"><i class="bi bi-lightning-charge me-1 text-warning"></i> Quick Tips</h6>
                    <div class="vstack gap-2">
                        <div class="d-flex align-items-start gap-2">
                            <span class="badge bg-light text-dark mt-1">💡</span>
                            <small class="text-muted">Include the merchant name in your description for better AI detection.</small>
                        </div>
                        <div class="d-flex align-items-start gap-2">
                            <span class="badge bg-light text-dark mt-1">💡</span>
                            <small class="text-muted">Use natural language like "Paid rent $1500" or "Bought groceries at Walmart".</small>
                        </div>
                        <div class="d-flex align-items-start gap-2">
                            <span class="badge bg-light text-dark mt-1">💡</span>
                            <small class="text-muted">AI can distinguish between one-time and recurring expenses automatically.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
document.getElementById('expenseForm')?.addEventListener('submit', function (e) {
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';
});
</script>
@endpush
