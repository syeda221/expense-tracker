<form method="POST" action="{{ route('profile.destroy') }}" class="form-premium" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
    @csrf
    @method('delete')

    <p style="font-size:13px;color:var(--text-muted);margin:0 0 16px;line-height:1.5">
        Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm.
    </p>

    <div style="margin-bottom:16px">
        <label for="password" class="form-label">Password</label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Enter your password to confirm">
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn-premium btn-danger">
        <i data-lucide="trash-2"></i>
        Delete Account
    </button>
</form>