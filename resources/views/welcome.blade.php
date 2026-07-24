<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Foresight') }} — AI Expense Manager</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .landing-glow {
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(ellipse at 50% 50%, rgba(14,207,179,0.08) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }
        .landing-content { position: relative; z-index: 1; }
    </style>
</head>
<body style="background:var(--bg-primary);display:flex;align-items:center;justify-content:center;min-height:100vh;padding:24px;font-family:'Inter',sans-serif">
    <div class="landing-glow"></div>
    <div class="landing-content" style="text-align:center;max-width:480px">
        <div style="width:56px;height:56px;background:linear-gradient(135deg,var(--primary),var(--accent));border-radius:14px;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:26px;color:#fff;margin:0 auto 24px;box-shadow:0 0 30px var(--primary-glow)">$</div>
        <h1 style="font-size:36px;font-weight:800;letter-spacing:-0.03em;color:var(--text);margin:0 0 8px">ExpenseTrack</h1>
        <p style="font-size:16px;color:var(--text-muted);margin:0 0 32px;line-height:1.6">AI-powered expense management. Track, categorize, and gain insights automatically.</p>

        @if (Route::has('login'))
            <div style="display:flex;gap:12px;justify-content:center;margin-bottom:40px">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-premium btn-primary lg">
                        <i data-lucide="layout-dashboard"></i>
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-premium btn-primary lg">
                        <i data-lucide="log-in"></i>
                        Log in
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-premium btn-secondary lg">
                            <i data-lucide="user-plus"></i>
                            Register
                        </a>
                    @endif
                @endauth
            </div>
        @endif

        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;text-align:center">
            <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:12px;padding:20px 12px">
                <div style="width:36px;height:36px;border-radius:8px;background:var(--primary-subtle);display:flex;align-items:center;justify-content:center;margin:0 auto 10px;color:var(--primary)"><i data-lucide="bot" style="width:18px;height:18px"></i></div>
                <p style="font-size:12px;font-weight:600;color:var(--text);margin:0 0 4px">AI Auto</p>
                <p style="font-size:11px;color:var(--text-dim);margin:0">Smart categorization</p>
            </div>
            <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:12px;padding:20px 12px">
                <div style="width:36px;height:36px;border-radius:8px;background:var(--success-subtle);display:flex;align-items:center;justify-content:center;margin:0 auto 10px;color:var(--success)"><i data-lucide="bar-chart-3" style="width:18px;height:18px"></i></div>
                <p style="font-size:12px;font-weight:600;color:var(--text);margin:0 0 4px">Analytics</p>
                <p style="font-size:11px;color:var(--text-dim);margin:0">Visual insights</p>
            </div>
            <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:12px;padding:20px 12px">
                <div style="width:36px;height:36px;border-radius:8px;background:rgba(35,217,122,0.12);display:flex;align-items:center;justify-content:center;margin:0 auto 10px;color:var(--accent)"><i data-lucide="sparkles" style="width:18px;height:18px"></i></div>
                <p style="font-size:12px;font-weight:600;color:var(--text);margin:0 0 4px">Smart Search</p>
                <p style="font-size:11px;color:var(--text-dim);margin:0">Ask anything</p>
            </div>
        </div>

        <p style="color:var(--text-dim);font-size:12px;margin-top:48px">&copy; {{ date('Y') }} ExpenseTrack. All rights reserved.</p>
    </div>

    <script>document.addEventListener('DOMContentLoaded',function(){lucide.createIcons()});</script>
</body>
</html>
