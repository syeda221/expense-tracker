<form method="POST" action="{{ route('login') }}" class="form-premium">
    @csrf

    <div style="margin-bottom:16px">
        <label for="email" class="form-label">Email</label>
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div style="margin-bottom:16px">
        <label for="password" class="form-label">Password</label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div style="margin-bottom:20px;display:flex;align-items:center;gap:8px">
        <input type="checkbox" id="remember_me" name="remember" style="accent-color:var(--primary);width:16px;height:16px">
        <label for="remember_me" style="font-size:13px;color:var(--text-muted);cursor:pointer">Remember me</label>
    </div>

    <div style="display:flex;align-items:center;justify-content:space-between">
        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" style="font-size:13px;color:var(--text-muted);text-decoration:none;transition:color var(--transition-fast)">Forgot password?</a>
        @endif
        <button type="submit" class="btn-premium btn-primary">Log in</button>
    </div>

    <div style="text-align:center;margin-top:20px;padding-top:16px;border-top:1px solid var(--border)">
        <span style="font-size:13px;color:var(--text-dim)">Don't have an account? </span>
        <a href="{{ route('register') }}" style="font-size:13px;color:var(--primary);text-decoration:none;font-weight:600">Register</a>
    </div>
</form>