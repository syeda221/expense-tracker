<x-app-layout>
    <div class="page-header fade-in">
        <div style="display:flex;align-items:center;justify-content:space-between">
            <div>
                <h1 class="page-title">Edit Expense</h1>
                <p class="page-subtitle">Update your expense details</p>
            </div>
            <a href="{{ route('expenses.index') }}" class="btn-premium btn-secondary">
                <i data-lucide="arrow-left"></i>
                Back
            </a>
        </div>
    </div>

    @if ($expense->ai_confidence !== null && $expense->ai_confidence < 0.5)
        <div class="alert-premium alert-error fade-in">
            <i data-lucide="alert-triangle" style="width:18px;height:18px;flex-shrink:0"></i>
            <span>AI couldn't confidently categorize this expense. You can edit the category manually.</span>
        </div>
    @endif

    <div style="display:grid;grid-template-columns:1.5fr 1fr;gap:24px" class="fade-in-up">
        <div>
            <div class="card-premium">
                <div class="card-body">
                    <form method="POST" action="{{ route('expenses.update', $expense) }}">
                        @csrf
                        @method('PUT')

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
                            <div class="form-premium">
                                <label for="amount" class="form-label">Amount <span style="color:var(--danger)">*</span></label>
                                <div style="display:flex">
                                    <span style="background:var(--bg-hover);border:1px solid var(--border);border-right:none;border-radius:8px 0 0 8px;padding:10px 14px;color:var(--text-muted);font-size:14px">RS</span>
                                    <input id="amount" type="number" step="0.01" min="0" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount', $expense->amount) }}" required style="border-radius:0 8px 8px 0">
                                </div>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-premium">
                                <label for="expense_date" class="form-label">Date <span style="color:var(--danger)">*</span></label>
                                <input id="expense_date" type="date" class="form-control @error('expense_date') is-invalid @enderror" name="expense_date" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required>
                                @error('expense_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-premium" style="margin-bottom:20px">
                            <label for="description" class="form-label">Description <span style="color:var(--danger)">*</span></label>
                            <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" rows="3" required style="min-height:90px">{{ old('description', $expense->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>



                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px">
                            <div class="form-premium">
                                <label for="category" class="form-label">Category</label>
                                <div style="margin-bottom:8px">
                                    <span class="badge-premium category">
                                        @switch($expense->category)
                                            @case('Food & Dining')<i data-lucide="utensils" style="width:14px;height:14px"></i>@break
                                            @case('Shopping')<i data-lucide="shopping-bag" style="width:14px;height:14px"></i>@break
                                            @case('Transport')<i data-lucide="car" style="width:14px;height:14px"></i>@break
                                            @case('Groceries')<i data-lucide="shopping-cart" style="width:14px;height:14px"></i>@break
                                            @case('Bills')<i data-lucide="file-text" style="width:14px;height:14px"></i>@break
                                            @case('Subscription')<i data-lucide="repeat" style="width:14px;height:14px"></i>@break
                                            @default<i data-lucide="circle-dollar" style="width:14px;height:14px"></i>
                                        @endswitch
                                        {{ $expense->category }}
                                    </span>
                                    @if ($expense->ai_confidence !== null && $expense->ai_confidence >= 0.8)
                                        <span class="badge-premium confidence-high" style="margin-left:6px">{{ number_format($expense->ai_confidence * 100, 0) }}%</span>
                                    @endif
                                </div>
                                <select id="category" class="form-select @error('category') is-invalid @enderror" name="category">
                                    <option value="">Keep current ({{ $expense->category }})</option>
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
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div style="font-size:12px;color:var(--text-dim);margin-top:4px">Select to override AI-detected category</div>
                            </div>
                            <div class="form-premium">
                                <label for="merchant" class="form-label">Merchant</label>
                                <input id="merchant" type="text" class="form-control @error('merchant') is-invalid @enderror" name="merchant" value="{{ old('merchant', $expense->merchant) }}" placeholder="Leave for AI detection">
                                @error('merchant')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if ($expense->merchant)
                                    <div style="font-size:12px;color:var(--text-dim);margin-top:4px">AI detected: <strong>{{ $expense->merchant }}</strong></div>
                                @endif
                            </div>
                        </div>

                        <div class="form-premium" style="margin-bottom:24px">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea id="notes" class="form-control @error('notes') is-invalid @enderror" name="notes" rows="2">{{ old('notes', $expense->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div style="display:flex;gap:12px">
                            <button type="submit" class="btn-premium btn-primary lg">
                                <i data-lucide="check"></i>
                                Update Expense
                            </button>
                            <a href="{{ route('expenses.index') }}" class="btn-premium btn-secondary lg">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div>
            <div class="card-premium card-gradient" style="margin-bottom:20px">
                <div class="card-body">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px">
                        <div style="width:36px;height:36px;border-radius:8px;background:var(--primary-subtle);display:flex;align-items:center;justify-content:center;color:var(--primary)">
                            <i data-lucide="bot"></i>
                        </div>
                        <h5 style="margin:0;font-size:15px">AI Classification</h5>
                    </div>

                    @if ($expense->ai_confidence !== null)
                        <div style="margin-bottom:16px">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                                <span style="font-size:12px;color:var(--text-dim);font-weight:500">Confidence Score</span>
                                <span style="font-size:13px;font-weight:700;color:{{ $expense->ai_confidence >= 0.8 ? 'var(--success)' : ($expense->ai_confidence >= 0.5 ? 'var(--warning)' : 'var(--danger)') }}">
                                    {{ number_format($expense->ai_confidence * 100, 0) }}%
                                </span>
                            </div>
                            <div class="progress-premium" style="height:8px">
                                <div class="progress-bar"
                                     style="width:{{ $expense->ai_confidence * 100 }}%;background:{{ $expense->ai_confidence >= 0.8 ? 'var(--success)' : ($expense->ai_confidence >= 0.5 ? 'var(--warning)' : 'var(--danger)') }}">
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="ai-insight-item">
                                <span class="ai-insight-label">Recurring</span>
                                <span class="ai-insight-value">{{ $expense->is_recurring ? 'Yes' : 'No' }}</span>
                            </div>
                            <div class="ai-insight-item">
                                <span class="ai-insight-label">Detected By</span>
                                <span class="ai-insight-value" style="display:flex;align-items:center;gap:4px">
                                    <i data-lucide="bot" style="width:14px;height:14px;color:var(--primary)"></i>
                                    AI
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card-premium">
                <div class="card-body">
                    <h5 style="font-size:15px;margin:0 0 16px;display:flex;align-items:center;gap:8px">
                        <i data-lucide="settings" style="width:16px;height:16px;color:var(--text-muted)"></i>
                        Quick Actions
                    </h5>
                    <div style="display:flex;flex-direction:column;gap:8px">
                        <a href="{{ route('expenses.show', $expense) }}" class="btn-premium btn-secondary w-100" style="justify-content:center">
                            <i data-lucide="eye"></i>
                            View Details
                        </a>
                        <form method="POST" action="{{ route('expenses.destroy', $expense) }}" onsubmit="return confirm('Delete this expense permanently?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-premium btn-danger w-100" style="justify-content:center">
                                <i data-lucide="trash-2"></i>
                                Delete Expense
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
