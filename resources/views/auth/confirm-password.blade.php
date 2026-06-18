<div class="mb-4 text-muted small">This is a secure area of the application. Please confirm your password before continuing.</div>

<form method="POST" action="{{ route('password.confirm') }}">
    @csrf

    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autofocus>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="d-flex align-items-center justify-content-end">
        <button type="submit" class="btn btn-primary">Confirm</button>
    </div>
</form>
