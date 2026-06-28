<x-guest-layout>
    <div class="login-brand" style="margin-bottom:24px">
        <div class="login-brand-icon">
            <i data-lucide="refresh-cw" style="width:22px;height:22px;color:#fff"></i>
        </div>
        <h4 class="login-brand-name">Reset Password</h4>
        <p class="login-brand-tagline">Choose a new password for your account</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="form-premium">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="login-field">
            <label for="email" class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i data-lucide="mail" style="width:16px;height:16px"></i>
                </span>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $request->email) }}" required autofocus placeholder="you@example.com">
            </div>
            @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="login-field">
            <label for="password" class="form-label">New Password</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i data-lucide="lock" style="width:16px;height:16px"></i>
                </span>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required placeholder="Min. 8 characters">
            </div>
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="login-field" style="margin-bottom:24px">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i data-lucide="lock" style="width:16px;height:16px"></i>
                </span>
                <input id="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required placeholder="Confirm your password">
            </div>
            @error('password_confirmation')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="login-field">
            <button type="submit" class="btn-premium btn-primary" style="width:100%;justify-content:center;padding:12px 24px">
                <i data-lucide="check-circle" style="width:17px;height:17px"></i>
                Reset Password
            </button>
        </div>
    </form>
</x-guest-layout>