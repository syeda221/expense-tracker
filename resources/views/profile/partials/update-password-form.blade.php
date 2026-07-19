<form method="POST" action="{{ route('profile.update') }}" class="form-premium">
    @csrf
    @method('patch')

    <div style="margin-bottom:16px">
        <label for="current_password" class="form-label">Current Password</label>
        <input id="current_password" type="password" class="form-control @error('current_password') is-invalid @enderror" name="current_password" required autocomplete="current-password">
        @error('current_password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div style="margin-bottom:16px">
        <label for="password" class="form-label">New Password</label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div style="margin-bottom:16px">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
    </div>

    <div style="display:flex;align-items:center;gap:12px">
        <button type="submit" class="btn-premium btn-primary">Save</button>
        @if (session('status') === 'password-updated')
            <span style="color:var(--success);font-size:13px;display:flex;align-items:center;gap:4px">
                <i data-lucide="check-circle" style="width:14px;height:14px"></i> Saved.
            </span>
        @endif
    </div>
</form>
