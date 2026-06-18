<div style="font-size:13px;color:var(--text-muted);margin-bottom:20px;line-height:1.5">Forgot your password? No problem. Let us know your email address and we will email you a password reset link.</div>

<form method="POST" action="{{ route('password.email') }}" class="form-premium">
    @csrf

    @if (session('status'))
        <div style="background:var(--success-subtle);border:1px solid rgba(34,197,94,0.2);border-radius:8px;padding:12px;font-size:13px;color:var(--success);margin-bottom:16px">{{ session('status') }}</div>
    @endif

    <div style="margin-bottom:16px">
        <label for="email" class="form-label">Email</label>
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div style="display:flex;align-items:center;justify-content:flex-end">
        <button type="submit" class="btn-premium btn-primary">Send Reset Link</button>
    </div>
</form>