<div style="font-size:13px;color:var(--text-muted);margin-bottom:20px;line-height:1.5">Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.</div>

@if (session('status') == 'verification-link-sent')
    <div style="background:var(--success-subtle);border:1px solid rgba(34,197,94,0.2);border-radius:8px;padding:12px;font-size:13px;color:var(--success);margin-bottom:16px">A new verification link has been sent to the email address you provided during registration.</div>
@endif

<div style="display:flex;align-items:center;justify-content:space-between;gap:12px">
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn-premium btn-primary">Resend Verification Email</button>
    </form>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-premium btn-secondary">Log Out</button>
    </form>
</div>