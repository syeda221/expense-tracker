<x-app-layout>
    <div class="page-header fade-in">
        <div style="display:flex;align-items:center;justify-content:space-between">
            <div>
                <h1 class="page-title">Expense Details</h1>
                <p class="page-subtitle">{{ $expense->expense_date->format('l, F j, Y') }}</p>
            </div>
            <div style="display:flex;gap:8px">
                <a href="{{ route('expenses.edit', $expense) }}" class="btn-premium btn-primary">
                    <i data-lucide="pencil"></i>
                    Edit
                </a>
                <a href="{{ route('expenses.index') }}" class="btn-premium btn-secondary">
                    <i data-lucide="arrow-left"></i>
                    Back
                </a>
            </div>
        </div>
    </div>

    @if ($expense->ai_confidence !== null && $expense->ai_confidence < 0.5)
        <div class="alert-premium alert-error fade-in" style="display:flex;align-items:center;justify-content:space-between;gap:12px">
            <div style="display:flex;align-items:center;gap:12px">
                <i data-lucide="alert-triangle" style="width:18px;height:18px;flex-shrink:0"></i>
                <span>Low AI Confidence ({{ number_format($expense->ai_confidence * 100, 0) }}%). <a href="{{ route('expenses.edit', $expense) }}" style="color:var(--danger);font-weight:600">Edit manually</a></span>
            </div>
            <video autoplay loop muted playsinline class="owl-video" style="width:86px;height:86px;flex-shrink:0;margin-left:8px;margin-top:-10px;margin-bottom:-10px">
                <source src="{{ asset('video/Owl_notices_spending_increase_202606250101.mp4') }}" type="video/mp4">
            </video>
        </div>
    @elseif ($expense->ai_confidence !== null && $expense->ai_confidence >= 0.5)
        <div class="alert-premium alert-success fade-in" style="display:flex;align-items:center;justify-content:space-between;gap:12px">
            <div style="display:flex;align-items:center;gap:12px">
                <i data-lucide="check-circle" style="width:18px;height:18px;flex-shrink:0"></i>
                <span>AI Categorized as <strong>{{ $expense->category }}</strong>
                    @if ($expense->merchant) from <strong>{{ $expense->merchant }}</strong> @endif
                    with {{ number_format($expense->ai_confidence * 100, 0) }}% confidence.</span>
            </div>
            <video autoplay loop muted playsinline class="owl-video" style="width:86px;height:86px;flex-shrink:0;margin-left:8px;margin-top:-10px;margin-bottom:-10px">
                <source src="{{ asset('video/Mascot_placing_wing_on_chin_202606242120.mp4') }}" type="video/mp4">
            </video>
        </div>
    @endif

    <div style="display:grid;grid-template-columns:1.5fr 1fr;gap:24px" class="fade-in-up">
        <div>
            <div class="card-premium">
                <div class="card-body">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px">
                        <div>
                            <h1 style="font-size:42px;font-weight:800;letter-spacing:-0.03em;margin:0 0 4px;background:linear-gradient(135deg,var(--text),var(--text-muted));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">
                                RS {{ number_format($expense->amount, 2) }}
                            </h1>
                            <p style="color:var(--text-muted);font-size:14px;margin:0;display:flex;align-items:center;gap:12px">
                                <span style="display:flex;align-items:center;gap:4px"><i data-lucide="calendar" style="width:14px;height:14px"></i> {{ $expense->expense_date->format('F d, Y') }}</span>
                                <span style="display:flex;align-items:center;gap:4px"><i data-lucide="credit-card" style="width:14px;height:14px"></i> {{ $expense->payment_method }}</span>
                            </p>
                        </div>
                        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px">
                            <span class="badge-premium category" style="font-size:14px;padding:6px 16px">
                                @switch($expense->category)
                                    @case('Food & Dining')<i data-lucide="utensils" style="width:16px;height:16px"></i>@break
                                    @case('Shopping')<i data-lucide="shopping-bag" style="width:16px;height:16px"></i>@break
                                    @case('Transport')<i data-lucide="car" style="width:16px;height:16px"></i>@break
                                    @case('Fuel')<i data-lucide="fuel" style="width:16px;height:16px"></i>@break
                                    @case('Groceries')<i data-lucide="shopping-cart" style="width:16px;height:16px"></i>@break
                                    @case('Bills')<i data-lucide="file-text" style="width:16px;height:16px"></i>@break
                                    @case('Utilities')<i data-lucide="zap" style="width:16px;height:16px"></i>@break
                                    @case('Healthcare')<i data-lucide="heart-pulse" style="width:16px;height:16px"></i>@break
                                    @case('Education')<i data-lucide="book-open" style="width:16px;height:16px"></i>@break
                                    @case('Entertainment')<i data-lucide="film" style="width:16px;height:16px"></i>@break
                                    @case('Travel')<i data-lucide="plane" style="width:16px;height:16px"></i>@break
                                    @case('Rent')<i data-lucide="home" style="width:16px;height:16px"></i>@break
                                    @case('Subscription')<i data-lucide="repeat" style="width:16px;height:16px"></i>@break
                                    @case('Investment')<i data-lucide="trending-up" style="width:16px;height:16px"></i>@break
                                    @default<i data-lucide="circle-dollar" style="width:16px;height:16px"></i>
                                @endswitch
                                {{ $expense->category }}
                            </span>
                        </div>
                    </div>

                    <div style="margin-bottom:24px">
                        <p style="font-size:17px;line-height:1.6;color:var(--text);margin:0">{{ $expense->description }}</p>
                    </div>

                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:24px">
                        <div style="background:var(--bg-hover);border-radius:12px;padding:14px">
                            <p style="font-size:11px;color:var(--text-dim);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin:0 0 6px">Merchant</p>
                            <p style="font-size:15px;font-weight:600;margin:0;color:var(--text)">{{ $expense->merchant ?? 'Not detected' }}</p>
                        </div>
                        <div style="background:var(--bg-hover);border-radius:12px;padding:14px">
                            <p style="font-size:11px;color:var(--text-dim);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin:0 0 6px">Payment</p>
                            <p style="font-size:15px;font-weight:600;margin:0;color:var(--text)">{{ $expense->payment_method }}</p>
                        </div>
                        <div style="background:var(--bg-hover);border-radius:12px;padding:14px">
                            <p style="font-size:11px;color:var(--text-dim);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin:0 0 6px">Recurring</p>
                            <p style="font-size:15px;font-weight:600;margin:0;color:var(--text)">
                                @if ($expense->is_recurring)
                                    <span style="color:var(--accent)"><i data-lucide="repeat" style="width:14px;height:14px;display:inline"></i> Yes</span>
                                @else
                                    No
                                @endif
                            </p>
                        </div>
                    </div>

                    @if ($expense->notes)
                        <div>
                            <p style="font-size:11px;color:var(--text-dim);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin:0 0 6px">Notes</p>
                            <p style="font-size:14px;color:var(--text-muted);margin:0;line-height:1.6">{{ $expense->notes }}</p>
                        </div>
                    @endif
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
                        <div style="margin-bottom:20px">
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
                                <span class="ai-insight-label">Category</span>
                                <span class="ai-insight-value">{{ $expense->category }}</span>
                            </div>
                            <div class="ai-insight-item">
                                <span class="ai-insight-label">Merchant</span>
                                <span class="ai-insight-value">{{ $expense->merchant ?? 'N/A' }}</span>
                            </div>
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

                        @if ($expense->ai_confidence < 0.5)
                            <div style="background:var(--danger-subtle);border-radius:8px;padding:12px;margin-top:16px;font-size:12px;color:var(--danger);display:flex;align-items:flex-start;gap:8px">
                                <i data-lucide="alert-triangle" style="width:16px;height:16px;flex-shrink:0;margin-top:1px"></i>
                                <span>AI confidence is low. <a href="{{ route('expenses.edit', $expense) }}" style="color:var(--danger);font-weight:600">Review manually</a></span>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <div class="card-premium">
                <div class="card-body">
                    <h5 style="font-size:15px;margin:0 0 16px;display:flex;align-items:center;gap:8px">
                        <i data-lucide="settings" style="width:16px;height:16px;color:var(--text-muted)"></i>
                        Actions
                    </h5>
                    <div style="display:flex;flex-direction:column;gap:8px">
                        <a href="{{ route('expenses.edit', $expense) }}" class="btn-premium btn-primary w-100" style="justify-content:center">
                            <i data-lucide="pencil"></i>
                            Edit Expense
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
