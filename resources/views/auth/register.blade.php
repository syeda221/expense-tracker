<form method="POST" action="{{ route('register') }}" class="form-premium">
    @csrf

    <div style="margin-bottom:16px">
        <label for="name" class="form-label">Name</label>
        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div style="margin-bottom:16px">
        <label for="email" class="form-label">Email</label>
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div style="margin-bottom:16px">
        <label for="password" class="form-label">Password</label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div style="margin-bottom:20px">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
    </div>

    <div style="display:flex;align-items:center;justify-content:flex-end">
        <button type="submit" class="btn-premium btn-primary">Create Account</button>
    </div>

    <div style="text-align:center;margin-top:20px;padding-top:16px;border-top:1px solid var(--border)">
        <span style="font-size:13px;color:var(--text-dim)">Already have an account? </span>
        <a href="{{ route('login') }}" style="font-size:13px;color:var(--primary);text-decoration:none;font-weight:600">Log in</a>
    </div>
</form>