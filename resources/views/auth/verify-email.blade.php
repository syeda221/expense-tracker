<div class="mb-4 text-muted small">Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.</div>

@if (session('status') == 'verification-link-sent')
    <div class="alert alert-success">A new verification link has been sent to the email address you provided during registration.</div>
@endif

<div class="d-flex align-items-center justify-content-between">
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn btn-primary">Resend Verification Email</button>
    </form>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-outline-secondary">Log Out</button>
    </form>
</div>
