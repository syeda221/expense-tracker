<div class="mb-4 text-muted small">Forgot your password? No problem. Let us know your email address and we will email you a password reset link.</div>

<form method="POST" action="{{ route('password.email') }}">
    @csrf

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="d-flex align-items-center justify-content-end">
        <button type="submit" class="btn btn-primary">Send Reset Link</button>
    </div>
</form>
