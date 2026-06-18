<form method="POST" action="{{ route('profile.destroy') }}">
    @csrf
    @method('delete')

    <p class="text-muted small">Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.</p>

    <div class="mb-3">
        <label for="password" class="form-label">Enter your password to confirm</label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-danger">Delete Account</button>
</form>
