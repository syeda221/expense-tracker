<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
        <label class="form-check-label" for="remember_me">Remember me</label>
    </div>

    <div class="d-flex align-items-center justify-content-between">
        @if (Route::has('password.request'))
            <a class="text-decoration-none small" href="{{ route('password.request') }}">Forgot password?</a>
        @endif
        <button type="submit" class="btn btn-primary">Log in</button>
    </div>

    <div class="text-center mt-3">
        <span class="text-muted small">Don't have an account?</span>
        <a href="{{ route('register') }}" class="text-decoration-none small">Register</a>
    </div>
</form>
