<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Edit Expense</h4>
            <p class="text-muted small mb-0">Update your expense details</p>
        </div>
        <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    @if ($expense->ai_confidence === null)
        <div class="alert alert-info border-0 shadow-sm d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-robot fs-5"></i>
            <span>AI is still analyzing this expense. The category and merchant will be updated automatically.</span>
        </div>
    @elseif ($expense->ai_confidence < 0.5 && $expense->ai_confidence > 0)
        <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-exclamation-triangle fs-5"></i>
            <span>AI couldn't confidently categorize this expense. You can edit it manually.</span>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('expenses.update', $expense) }}">
                @csrf
                @method('PUT')

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
                                    <input id="amount" type="number" step="0.01" min="0" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount', $expense->amount) }}" required>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="expense_date" class="form-label fw-medium">Date <span class="text-danger">*</span></label>
                                <input id="expense_date" type="date" class="form-control form-control-lg @error('expense_date') is-invalid @enderror" name="expense_date" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required>
                                @error('expense_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-medium">Description <span class="text-danger">*</span></label>
                            <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" rows="3" required>{{ old('description', $expense->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="payment_method" class="form-label fw-medium">Payment Method <span class="text-danger">*</span></label>
                            <select id="payment_method" class="form-select @error('payment_method') is-invalid @enderror" name="payment_method" required>
                                <option value="">Select method</option>
                                <option value="Cash" @selected(old('payment_method', $expense->payment_method) == 'Cash')>Cash</option>
                                <option value="Credit Card" @selected(old('payment_method', $expense->payment_method) == 'Credit Card')>Credit Card</option>
                                <option value="Debit Card" @selected(old('payment_method', $expense->payment_method) == 'Debit Card')>Debit Card</option>
                                <option value="UPI" @selected(old('payment_method', $expense->payment_method) == 'UPI')>UPI</option>
                                <option value="Bank Transfer" @selected(old('payment_method', $expense->payment_method) == 'Bank Transfer')>Bank Transfer</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-semibold mb-0"><i class="bi bi-tag me-1"></i> Classification</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="category" class="form-label fw-medium">Category</label>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    @if ($expense->ai_confidence !== null)
                                        <span class="badge bg-{{ $expense->category !== 'Other' ? 'success' : 'secondary' }} rounded-pill px-3 py-2 fs-6">
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
                                        <span class="badge bg-info rounded-pill px-3 py-2 fs-6">
                                            <i class="bi bi-robot me-1"></i> Analyzing...
                                        </span>
                                    @endif
                                </div>
                                <select id="category" class="form-select @error('category') is-invalid @enderror" name="category">
                                    <option value="">Select to override AI</option>
                                    <option value="Food & Dining" @selected(old('category', $expense->category) == 'Food & Dining')>Food & Dining</option>
                                    <option value="Shopping" @selected(old('category', $expense->category) == 'Shopping')>Shopping</option>
                                    <option value="Transport" @selected(old('category', $expense->category) == 'Transport')>Transport</option>
                                    <option value="Fuel" @selected(old('category', $expense->category) == 'Fuel')>Fuel</option>
                                    <option value="Groceries" @selected(old('category', $expense->category) == 'Groceries')>Groceries</option>
                                    <option value="Bills" @selected(old('category', $expense->category) == 'Bills')>Bills</option>
                                    <option value="Utilities" @selected(old('category', $expense->category) == 'Utilities')>Utilities</option>
                                    <option value="Healthcare" @selected(old('category', $expense->category) == 'Healthcare')>Healthcare</option>
                                    <option value="Education" @selected(old('category', $expense->category) == 'Education')>Education</option>
                                    <option value="Entertainment" @selected(old('category', $expense->category) == 'Entertainment')>Entertainment</option>
                                    <option value="Travel" @selected(old('category', $expense->category) == 'Travel')>Travel</option>
                                    <option value="Rent" @selected(old('category', $expense->category) == 'Rent')>Rent</option>
                                    <option value="Investment" @selected(old('category', $expense->category) == 'Investment')>Investment</option>
                                    <option value="Salary" @selected(old('category', $expense->category) == 'Salary')>Salary</option>
                                    <option value="Subscription" @selected(old('category', $expense->category) == 'Subscription')>Subscription</option>
                                    <option value="Other" @selected(old('category', $expense->category) == 'Other')>Other</option>
                                </select>
                                @if ($expense->ai_confidence !== null)
                                    <div class="form-text">Leave empty to keep AI-detected category.</div>
                                @else
                                    <div class="form-text">AI has not finished analyzing. Select a category manually if needed.</div>
                                @endif
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="merchant" class="form-label fw-medium">Merchant</label>
                                <input id="merchant" type="text" class="form-control @error('merchant') is-invalid @enderror" name="merchant" value="{{ old('merchant', $expense->merchant) }}" placeholder="Leave blank to let AI detect the merchant.">
                                @error('merchant')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if ($expense->ai_confidence !== null)
                                    <div class="form-text">AI-detected: <strong>{{ $expense->merchant ?? 'Not detected' }}</strong></div>
                                @endif
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
                        <textarea id="notes" class="form-control @error('notes') is-invalid @enderror" name="notes" rows="3">{{ old('notes', $expense->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-lg px-4">
                        <i class="bi bi-check-lg"></i> Update Expense
                    </button>
                    <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary btn-lg">Cancel</a>
                </div>
            </form>
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
                            <small class="text-muted">Recurring</small>
                            <strong>
                                @if ($expense->is_recurring)
                                    <span class="text-primary"><i class="bi bi-arrow-repeat"></i> Yes</span>
                                @else
                                    No
                                @endif
                            </strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">Detected By</small>
                            <strong><i class="bi bi-robot text-primary"></i> AI</strong>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <div class="spinner-border text-primary mb-2" role="status" style="width: 2rem; height: 2rem;">
                            </div>
                            <p class="text-muted small mb-0">AI is analyzing this expense...</p>
                            <p class="text-muted small mb-0 mt-1">Category and merchant will be detected automatically.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-shield-check me-1"></i> Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('expenses.show', $expense) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-eye"></i> View Details
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
