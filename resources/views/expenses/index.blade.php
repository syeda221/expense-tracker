<x-app-layout>
    <div class="page-header fade-in">
        <div style="display:flex;align-items:center;justify-content:space-between">
            <div>
                <h1 class="page-title">Expenses</h1>
                <p class="page-subtitle">{{ $expenses->total() }} total expenses · {{ now()->format('F Y') }}</p>
            </div>
            <a href="{{ route('expenses.create') }}" class="btn-premium btn-primary">
                <i data-lucide="plus"></i>
                Add Expense
            </a>
        </div>
    </div>

    <div class="card-premium fade-in-up">
        <div class="card-body">
            <div class="filter-bar">
                <form method="GET" action="{{ route('expenses.index') }}">
                    <input type="text" name="search" class="filter-input filter-search" placeholder="Search description or merchant..." value="{{ $filters['search'] ?? '' }}">
                    <input type="text" name="category" class="filter-input filter-sm" placeholder="Category" value="{{ $filters['category'] ?? '' }}">
                    <input type="text" name="payment_method" class="filter-input filter-sm" placeholder="Payment" value="{{ $filters['payment_method'] ?? '' }}">
                    <input type="date" name="date_from" class="filter-input filter-date" value="{{ $filters['date_from'] ?? '' }}">
                    <input type="date" name="date_to" class="filter-input filter-date" value="{{ $filters['date_to'] ?? '' }}">
                    <button type="submit" class="btn-premium btn-primary sm">
                        <i data-lucide="search"></i>
                        Search
                    </button>
                    @if (request()->anyFilled(['search', 'category', 'payment_method', 'date_from', 'date_to']))
                        <a href="{{ route('expenses.index') }}" class="btn-premium btn-secondary sm">Clear</a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <div class="card-premium fade-in-up stagger-2" style="margin-top:20px;overflow:hidden">
        <div style="overflow-x:auto">
            <table class="table-premium">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Merchant</th>
                        <th>Payment</th>
                        <th style="text-align:right">Amount</th>
                        <th style="text-align:center">AI</th>
                        <th style="text-align:right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($expenses as $expense)
                        <tr>
                            <td style="white-space:nowrap;font-size:13px;color:var(--text-dim)">{{ $expense->expense_date->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('expenses.show', $expense) }}" style="text-decoration:none;color:var(--text);font-weight:600;font-size:14px;transition:color var(--transition-fast)">
                                    {{ Str::limit($expense->description, 40) }}
                                </a>
                            </td>
                            <td>
                                <span class="badge-premium category">{{ $expense->category }}</span>
                            </td>
                            <td style="font-size:13px;color:var(--text-muted)">{{ $expense->merchant ?? '—' }}</td>
                            <td style="font-size:13px;color:var(--text-muted)">{{ $expense->payment_method }}</td>
                            <td class="amount-cell">RS {{ number_format($expense->amount, 2) }}</td>
                            <td style="text-align:center">
                                @if ($expense->ai_confidence !== null)
                                    @if ($expense->ai_confidence >= 0.8)
                                        <span class="badge-premium confidence-high"><i data-lucide="check-circle" style="width:12px;height:12px"></i> {{ number_format($expense->ai_confidence * 100, 0) }}%</span>
                                    @elseif ($expense->ai_confidence >= 0.5)
                                        <span class="badge-premium confidence-medium"><i data-lucide="alert-circle" style="width:12px;height:12px"></i> {{ number_format($expense->ai_confidence * 100, 0) }}%</span>
                                    @else
                                        <span class="badge-premium confidence-low"><i data-lucide="x-circle" style="width:12px;height:12px"></i> {{ number_format($expense->ai_confidence * 100, 0) }}%</span>
                                    @endif
                                @endif
                            </td>
                            <td class="actions-cell">
                                <a href="{{ route('expenses.edit', $expense) }}" class="btn-premium btn-ghost sm" title="Edit">
                                    <i data-lucide="pencil" style="width:14px;height:14px"></i>
                                </a>
                                <form method="POST" action="{{ route('expenses.destroy', $expense) }}" style="display:inline" onsubmit="return confirm('Delete this expense?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-premium btn-ghost sm" title="Delete" style="color:var(--danger)">
                                        <i data-lucide="trash-2" style="width:14px;height:14px"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <div class="owl-container" style="margin-bottom: 16px;">
                                        <video autoplay loop muted playsinline class="owl-video" style="width:160px;height:160px;margin:-16px 0">
                                            <source src="{{ asset('video/Mascot_placing_wing_on_chin_202606242120.mp4') }}" type="video/mp4">
                                        </video>
                                    </div>
                                    <p class="empty-state-title">No expenses found</p>
                                    <p class="empty-state-text">Start tracking your expenses with AI-powered categorization</p>
                                    <a href="{{ route('expenses.create') }}" class="btn-premium btn-primary">
                                        <i data-lucide="plus"></i>
                                        Add your first expense
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($expenses->hasPages())
            <div style="padding:16px 24px;border-top:1px solid var(--border);display:flex;justify-content:center">
                {{ $expenses->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</x-app-layout>
