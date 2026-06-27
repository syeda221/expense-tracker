<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Expense Tracker') }} — AI Expense Manager</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="min-vh-100 d-flex align-items-center justify-content-center" style="background:var(--bg-primary);padding:24px">
        <div style="width:100%;max-width:440px">
            <div class="text-center mb-4">
                <div style="width:48px;height:48px;background:linear-gradient(135deg,var(--primary),var(--accent));border-radius:12px;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:22px;color:#fff;margin:0 auto 16px">$</div>
                <h4 style="font-weight:700;letter-spacing:-0.02em;color:var(--text);margin-bottom:4px">ExpenseTrack</h4>
                <p style="color:var(--text-muted);font-size:14px;margin:0">AI-powered expense management</p>
            </div>
            <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:18px;padding:32px;box-shadow:0 1px 3px rgba(0,0,0,0.3),0 1px 2px rgba(0,0,0,0.2)">
                {{ $slot }}
            </div>
            <p style="text-align:center;color:var(--text-dim);font-size:12px;margin-top:20px">&copy; {{ date('Y') }} ExpenseTrack. All rights reserved.</p>
        </div>
    </div>
    <script>document.addEventListener('DOMContentLoaded',function(){lucide.createIcons()});</script>
</body>
</html>