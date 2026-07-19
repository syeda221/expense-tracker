<x-guest-layout>
    {{-- Owl Mascot --}}
    <div class="login-mascot">
        <div class="mascot-wrapper">
            <div class="mascot-glow"></div>
            <video autoplay loop muted playsinline class="owl-video mascot-video">
                <source src="{{ asset('video/Mascot_placing_wing_on_chin_202606242120.mp4') }}" type="video/mp4">
            </video>
        </div>
        <div class="speech-bubble">
            <div class="speech-arrow"></div>
            <p class="speech-text">Welcome back! <strong>Ready to track your expenses?</strong></p>
        </div>
    </div>

    {{-- Brand --}}
    <div class="login-brand">
        <div class="brand-icon">$</div>
        <h1 class="brand-name">ExpenseTrack</h1>
        <p class="brand-tagline">AI-powered expense management</p>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('login') }}" class="login-form" id="loginForm">
        @csrf

        @if (session('status'))
            <div class="form-alert form-alert-success">{{ session('status') }}</div>
        @endif

        {{-- Email --}}
        <div class="form-group">
            <label for="email">Email address</label>
            <div class="input-wrapper">
                <i data-lucide="mail" class="input-icon"></i>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       required autofocus placeholder="you@example.com" autocomplete="email"
                       class="@error('email') is-invalid @enderror">
                @error('email')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Password --}}
        <div class="form-group">
            <div class="label-row">
                <label for="password">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                @endif
            </div>
            <div class="input-wrapper">
                <i data-lucide="lock" class="input-icon"></i>
                <input id="password" type="password" name="password" required
                       placeholder="Enter your password" autocomplete="current-password"
                       class="@error('password') is-invalid @enderror">
                <button type="button" class="pwd-toggle" onclick="togglePassword()" tabindex="-1" aria-label="Toggle password visibility">
                    <i data-lucide="eye" id="pwdIcon"></i>
                </button>
                @error('password')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Remember --}}
        <div class="form-group-remember">
            <label class="remember-label">
                <input type="checkbox" name="remember" id="remember_me">
                <span class="remember-check"></span>
                <span class="remember-text">Remember me</span>
            </label>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-signin" id="signinBtn">
            <span class="btn-text">Sign in</span>
            <span class="btn-loader" style="display:none">
                <svg class="spinner" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                    <circle cx="12" cy="12" r="10" stroke-opacity="0.25"/>
                    <path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"/>
                </svg>
            </span>
        </button>
    </form>

    {{-- Register --}}
    <div class="login-footer">
        Don't have an account? <a href="{{ route('register') }}">Create one</a>
    </div>

    @push('scripts')
    <script>
    function togglePassword() {
        const pwd = document.getElementById('password');
        const icon = document.getElementById('pwdIcon');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.setAttribute('data-lucide', 'eye-off');
        } else {
            pwd.type = 'password';
            icon.setAttribute('data-lucide', 'eye');
        }
        lucide.createIcons();
    }

    document.getElementById('loginForm').addEventListener('submit', function() {
        const btn = document.getElementById('signinBtn');
        btn.querySelector('.btn-text').textContent = 'Signing in...';
        btn.querySelector('.btn-loader').style.display = 'flex';
    });
    </script>
    @endpush
</x-guest-layout>
