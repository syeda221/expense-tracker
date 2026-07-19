<x-guest-layout>
    <div class="login-brand" style="margin-bottom:24px">
        <div class="login-brand-icon">
            <i data-lucide="shield" style="width:22px;height:22px;color:#fff"></i>
        </div>
        <h4 class="login-brand-name">Confirm Password</h4>
        <p class="login-brand-tagline" style="max-width:300px;margin:0 auto">This is a secure area. Please confirm your password before continuing.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="form-premium">
        @csrf

        <div class="login-field">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i data-lucide="lock" style="width:16px;height:16px"></i>
                </span>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autofocus placeholder="Enter your password">
            </div>
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="login-field" style="margin-bottom:0">
            <button type="submit" class="btn-premium btn-primary" style="width:100%;justify-content:center;padding:12px 24px">
                <i data-lucide="check-circle" style="width:17px;height:17px"></i>
                Confirm
            </button>
        </div>
    </form>
</x-guest-layout>
