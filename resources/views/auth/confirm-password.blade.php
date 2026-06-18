<div style="font-size:13px;color:var(--text-muted);margin-bottom:20px;line-height:1.5">This is a secure area of the application. Please confirm your password before continuing.</div>

<form method="POST" action="{{ route('password.confirm') }}" class="form-premium">
    @csrf

    <div style="margin-bottom:20px">
        <label for="password" class="form-label">Password</label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autofocus>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div style="display:flex;align-items:center;justify-content:flex-end">
        <button type="submit" class="btn-premium btn-primary">Confirm</button>
    </div>
</form>