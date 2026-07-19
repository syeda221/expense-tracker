<x-app-layout>
    <div class="page-header fade-in">
        <h1 class="page-title">Settings</h1>
        <p class="page-subtitle">Manage your profile, password, and account settings</p>
    </div>

    <div style="display:grid;grid-template-columns:1fr;gap:24px;max-width:768px" class="fade-in-up">
        <div class="card-premium">
            <div class="card-body">
                <h5 style="font-size:15px;margin:0 0 20px;display:flex;align-items:center;gap:8px">
                    <i data-lucide="user" style="width:16px;height:16px;color:var(--text-muted)"></i>
                    Profile Information
                </h5>
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="card-premium">
            <div class="card-body">
                <h5 style="font-size:15px;margin:0 0 20px;display:flex;align-items:center;gap:8px">
                    <i data-lucide="lock" style="width:16px;height:16px;color:var(--text-muted)"></i>
                    Update Password
                </h5>
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="card-premium">
            <div class="card-body">
                <h5 style="font-size:15px;margin:0 0 20px;display:flex;align-items:center;gap:8px;color:var(--danger)">
                    <i data-lucide="trash-2" style="width:16px;height:16px"></i>
                    Delete Account
                </h5>
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
