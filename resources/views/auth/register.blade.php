<x-guest-layout>
    <div class="login-brand" style="margin-bottom:24px">
        <div class="login-brand-icon">$</div>
        <h4 class="login-brand-name">Create Account</h4>
        <p class="login-brand-tagline">Join ExpenseTrack to manage your finances</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="form-premium">
        @csrf

        <div class="login-field">
            <label for="name" class="form-label">Name</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i data-lucide="user" style="width:16px;height:16px"></i>
                </span>
                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus placeholder="Your full name">
            </div>
            @error('name')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="login-field">
            <label for="email" class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i data-lucide="mail" style="width:16px;height:16px"></i>
                </span>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required placeholder="you@example.com">
            </div>
            @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="login-field">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i data-lucide="lock" style="width:16px;height:16px"></i>
                </span>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Min. 8 characters">
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
                <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required placeholder="Confirm your password">
            </div>
        </div>

        <div class="login-field">
            <button type="submit" class="btn-premium btn-primary" style="width:100%;justify-content:center;padding:12px 24px">
                <i data-lucide="user-plus" style="width:17px;height:17px"></i>
                Create Account
            </button>
        </div>

        <div class="login-divider">
            <span>Already have an account? </span>
            <a href="{{ route('login') }}">Log in</a>
        </div>
    </form>
</x-guest-layout>
