<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Add Expense</h4>
        <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('expenses.store') }}">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input id="amount" type="number" step="0.01" min="0" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount') }}" required>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="expense_date" class="form-label">Date <span class="text-danger">*</span></label>
                                <input id="expense_date" type="date" class="form-control @error('expense_date') is-invalid @enderror" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required>
                                @error('expense_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" rows="2" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                <select id="category" class="form-select @error('category') is-invalid @enderror" name="category" required>
                                    <option value="">Select category</option>
                                    <option value="Food & Dining" @selected(old('category') == 'Food & Dining')>Food & Dining</option>
                                    <option value="Shopping" @selected(old('category') == 'Shopping')>Shopping</option>
                                    <option value="Transport" @selected(old('category') == 'Transport')>Transport</option>
                                    <option value="Fuel" @selected(old('category') == 'Fuel')>Fuel</option>
                                    <option value="Groceries" @selected(old('category') == 'Groceries')>Groceries</option>
                                    <option value="Bills" @selected(old('category') == 'Bills')>Bills</option>
                                    <option value="Utilities" @selected(old('category') == 'Utilities')>Utilities</option>
                                    <option value="Healthcare" @selected(old('category') == 'Healthcare')>Healthcare</option>
                                    <option value="Education" @selected(old('category') == 'Education')>Education</option>
                                    <option value="Entertainment" @selected(old('category') == 'Entertainment')>Entertainment</option>
                                    <option value="Travel" @selected(old('category') == 'Travel')>Travel</option>
                                    <option value="Rent" @selected(old('category') == 'Rent')>Rent</option>
                                    <option value="Investment" @selected(old('category') == 'Investment')>Investment</option>
                                    <option value="Salary" @selected(old('category') == 'Salary')>Salary</option>
                                    <option value="Subscription" @selected(old('category') == 'Subscription')>Subscription</option>
                                    <option value="Other" @selected(old('category') == 'Other')>Other</option>
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                                <select id="payment_method" class="form-select @error('payment_method') is-invalid @enderror" name="payment_method" required>
                                    <option value="">Select method</option>
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

                        <div class="mb-3">
                            <label for="merchant" class="form-label">Merchant</label>
                            <input id="merchant" type="text" class="form-control @error('merchant') is-invalid @enderror" name="merchant" value="{{ old('merchant') }}">
                            @error('merchant')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea id="notes" class="form-control @error('notes') is-invalid @enderror" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Save Expense
                            </button>
                            <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-info-circle"></i> Tips</h6>
                    <ul class="small text-muted mb-0 ps-3">
                        <li class="mb-1">Enter the exact amount you spent.</li>
                        <li class="mb-1">The date should be when the expense occurred.</li>
                        <li class="mb-1">Adding a merchant helps track spending patterns.</li>
                        <li class="mb-1">AI will auto-categorize and detect recurring expenses.</li>
                        <li class="mb-1">You can edit any field after saving.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
