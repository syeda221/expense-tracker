<form method="POST" action="{{ route('profile.update') }}" class="form-premium">
    @csrf
    @method('patch')

    <div style="margin-bottom:16px">
        <label for="name" class="form-label">Name</label>
        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $user->name) }}" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div style="margin-bottom:16px">
        <label for="email" class="form-label">Email</label>
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email) }}" required>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div style="display:flex;align-items:center;gap:12px">
        <button type="submit" class="btn-premium btn-primary">Save</button>
        @if (session('status') === 'profile-updated')
            <span style="color:var(--success);font-size:13px;display:flex;align-items:center;gap:4px">
                <i data-lucide="check-circle" style="width:14px;height:14px"></i> Saved.
            </span>
        @endif
    </div>
</form>