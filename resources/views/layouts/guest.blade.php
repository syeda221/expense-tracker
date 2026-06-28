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
    @stack('styles')
</head>
<body>
    <div class="min-vh-100 d-flex align-items-center justify-content-center" style="background:var(--bg-primary);padding:24px;position:relative;overflow:hidden">
        {{-- Ambient background orbs --}}
        <div style="position:fixed;top:-30%;left:-20%;width:600px;height:600px;background:radial-gradient(circle, rgba(22,199,183,0.07) 0%, transparent 60%);border-radius:50%;pointer-events:none;z-index:0;animation: orbFloat 20s ease-in-out infinite alternate;"></div>
        <div style="position:fixed;bottom:-20%;right:-15%;width:500px;height:500px;background:radial-gradient(circle, rgba(59,130,246,0.05) 0%, transparent 60%);border-radius:50%;pointer-events:none;z-index:0;animation: orbFloat 25s ease-in-out infinite alternate-reverse;"></div>
        <div style="position:fixed;top:40%;left:60%;width:300px;height:300px;background:radial-gradient(circle, rgba(14,207,179,0.04) 0%, transparent 60%);border-radius:50%;pointer-events:none;z-index:0;animation: orbFloat 18s ease-in-out infinite alternate;"></div>

        @if (request()->routeIs('login'))
            <div style="width:100%;max-width:480px;position:relative;z-index:1">
                <div style="position:relative;background:var(--bg-card);border:1px solid var(--border-light);border-radius:24px;padding:32px 32px 28px;box-shadow:0 2px 12px rgba(0,0,0,0.04),0 8px 32px rgba(0,0,0,0.03);overflow:visible;">
                    {{ $slot }}
                </div>
                <p style="text-align:center;color:var(--text-dim);font-size:12px;margin-top:20px">&copy; {{ date('Y') }} ExpenseTrack. All rights reserved.</p>
            </div>
        @else
            <div style="width:100%;max-width:440px;position:relative;z-index:1">
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
        @endif
    </div>

    <script>
    document.addEventListener('DOMContentLoaded',function(){
        lucide.createIcons();
        const playVideos = () => {
            document.querySelectorAll('.owl-video').forEach(v => {
                v.defaultMuted = true;
                v.muted = true;
                v.setAttribute('muted', 'muted');
                const playPromise = v.play();
                if (playPromise !== undefined) {
                    playPromise.catch(e => {});
                }
            });
        };
        playVideos();
        const interactionEvents = ['click', 'touchstart', 'scroll', 'keydown'];
        const onInteract = () => {
            playVideos();
            interactionEvents.forEach(evt => document.removeEventListener(evt, onInteract));
        };
        interactionEvents.forEach(evt => document.addEventListener(evt, onInteract, { once: true }));
    });
    </script>
    @stack('scripts')
</body>
</html>