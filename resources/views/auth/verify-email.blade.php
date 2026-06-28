<x-guest-layout>
    <div class="login-brand" style="margin-bottom:24px">
        <div class="login-brand-icon">
            <i data-lucide="mail-check" style="width:22px;height:22px;color:#fff"></i>
        </div>
        <h4 class="login-brand-name">Verify Email</h4>
        <p class="login-brand-tagline" style="max-width:320px;margin:0 auto">Thanks for signing up! Before getting started, please verify your email address.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="login-alert login-alert-success fade-in">A new verification link has been sent to the email address you provided during registration.</div>
    @endif

    <div style="padding:20px;background:var(--bg-hover);border-radius:12px;margin-bottom:24px;text-align:center">
        <p style="margin:0 0 16px;font-size:13px;color:var(--text-muted);line-height:1.6">If you didn't receive the email, we will gladly send you another.</p>
        <div style="display:flex;gap:12px;justify-content:center">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn-premium btn-primary" style="padding:10px 20px">
                    <i data-lucide="send" style="width:16px;height:16px"></i>
                    Resend Verification
                </button>
            </form>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-premium btn-ghost" style="padding:10px 20px">
                    <i data-lucide="log-out" style="width:16px;height:16px"></i>
                    Log Out
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>