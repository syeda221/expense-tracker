<x-guest-layout>
    <div class="login-brand" style="margin-bottom:24px">
        <div class="login-brand-icon">
            <i data-lucide="key-round" style="width:22px;height:22px;color:#fff"></i>
        </div>
        <h4 class="login-brand-name">Reset Password</h4>
        <p class="login-brand-tagline">Forgot your password? Enter your email to receive a reset link.</p>
    </div>

    <form method="POST" action="{{ route('password.email') }}" class="form-premium">
        @csrf

        @if (session('status'))
            <div class="login-alert login-alert-success fade-in">{{ session('status') }}</div>
        @endif

        <div class="login-field">
            <label for="email" class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i data-lucide="mail" style="width:16px;height:16px"></i>
                </span>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus placeholder="you@example.com">
            </div>
            @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="login-field">
            <button type="submit" class="btn-premium btn-primary" style="width:100%;justify-content:center;padding:12px 24px">
                <i data-lucide="send" style="width:17px;height:17px"></i>
                Send Reset Link
            </button>
        </div>

        <div class="login-divider">
            <span>Remember your password? </span>
            <a href="{{ route('login') }}">Log in</a>
        </div>
    </form>
</x-guest-layout>
