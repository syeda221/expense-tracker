<x-app-layout>
    <div class="page-header fade-in">
        <div style="display:flex;align-items:center;justify-content:space-between">
            <div>
                <h1 class="page-title">Add Expense</h1>
                <p class="page-subtitle">AI will auto-categorize your expense after saving</p>
            </div>
            <a href="{{ route('expenses.index') }}" class="btn-premium btn-secondary">
                <i data-lucide="arrow-left"></i>
                Back
            </a>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1.5fr 1fr;gap:24px" class="fade-in-up">
        <div>
            <div class="card-premium">
                <div class="card-body">
                    <form method="POST" action="{{ route('expenses.store') }}" id="expenseForm">
                        @csrf

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
                            <div class="form-premium">
                                <label for="amount" class="form-label">Amount <span style="color:var(--danger)">*</span></label>
                                <div style="display:flex">
                                    <span style="background:var(--bg-hover);border:1px solid var(--border);border-right:none;border-radius:8px 0 0 8px;padding:10px 14px;color:var(--text-muted);font-size:14px">$</span>
                                    <input id="amount" type="number" step="0.01" min="0" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount') }}" placeholder="0.00" required style="border-radius:0 8px 8px 0">
                                </div>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-premium">
                                <label for="expense_date" class="form-label">Date <span style="color:var(--danger)">*</span></label>
                                <input id="expense_date" type="date" class="form-control @error('expense_date') is-invalid @enderror" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required>
                                @error('expense_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-premium" style="margin-bottom:20px">
                            <label for="description" class="form-label">Description <span style="color:var(--danger)">*</span></label>
                            <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" rows="3" placeholder="e.g. Lunch at McDonald's, Uber ride to downtown, Monthly electricity bill..." required style="min-height:90px">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div style="font-size:12px;color:var(--text-dim);margin-top:4px">A detailed description helps AI categorize accurately.</div>
                        </div>

                        <div class="form-premium" style="margin-bottom:20px">
                            <label for="payment_method" class="form-label">Payment Method <span style="color:var(--danger)">*</span></label>
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

                        <div class="form-premium" style="margin-bottom:24px">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea id="notes" class="form-control @error('notes') is-invalid @enderror" name="notes" rows="2" placeholder="Any additional information...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div style="display:flex;gap:12px">
                            <button type="submit" class="btn-premium btn-primary lg" id="saveBtn">
                                <i data-lucide="check"></i>
                                Save Expense
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
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
                        <div style="width:40px;height:40px;border-radius:10px;background:var(--primary-subtle);display:flex;align-items:center;justify-content:center;color:var(--primary)">
                            <i data-lucide="bot"></i>
                        </div>
                        <div>
                            <h5 style="margin:0;font-size:16px">AI Assistant</h5>
                            <p style="margin:0;font-size:12px;color:var(--text-dim)">Intelligent expense manager</p>
                        </div>
                    </div>

                    <div class="ai-box">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
                            <span class="badge-premium ai">AI</span>
                            <span style="font-size:13px;font-weight:600;color:var(--text)">Auto Categorized</span>
                        </div>
                        <p style="font-size:13px;color:var(--text-muted);margin:0">Category is detected automatically by AI. No need to select one.</p>
                    </div>

                    <div style="margin-top:16px">
                        <input type="hidden" name="category" value="Other">
                        <div class="form-premium" style="margin-bottom:12px">
                            <label for="merchant" class="form-label">Merchant <span style="color:var(--text-dim);font-weight:400">(optional)</span></label>
                            <input id="merchant" type="text" class="form-control @error('merchant') is-invalid @enderror" name="merchant" value="{{ old('merchant') }}" placeholder="Leave blank for AI detection">
                            @error('merchant')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div style="font-size:12px;color:var(--text-dim);margin-top:4px">Optional — AI can detect it from your description.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-premium">
                <div class="card-body">
                    <h5 style="font-size:15px;margin:0 0 16px;display:flex;align-items:center;gap:8px">
                        <i data-lucide="lightbulb" style="width:16px;height:16px;color:var(--warning)"></i>
                        Quick Tips
                    </h5>
                    <div style="display:flex;flex-direction:column;gap:12px">
                        <div style="display:flex;align-items:flex-start;gap:10px">
                            <span style="font-size:16px;flex-shrink:0">💡</span>
                            <small style="color:var(--text-dim);line-height:1.4">Include the merchant name in your description for better AI detection.</small>
                        </div>
                        <div style="display:flex;align-items:flex-start;gap:10px">
                            <span style="font-size:16px;flex-shrink:0">💡</span>
                            <small style="color:var(--text-dim);line-height:1.4">Use natural language like "Paid rent $1500" or "Bought groceries at Walmart".</small>
                        </div>
                        <div style="display:flex;align-items:flex-start;gap:10px">
                            <span style="font-size:16px;flex-shrink:0">💡</span>
                            <small style="color:var(--text-dim);line-height:1.4">AI can distinguish between one-time and recurring expenses automatically.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.getElementById('expenseForm')?.addEventListener('submit', function (e) {
        const btn = document.getElementById('saveBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" style="width:14px;height:14px;border-width:2px"></span> Saving...';
    });
    </script>
    @endpush
</x-app-layout>