<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Expense Tracker') }} — AI Expense Manager</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <div class="app-wrapper">
        <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

        <aside class="sidebar" id="sidebar">
            <div class="sidebar-logo">
                <div class="sidebar-logo-icon">$</div>
                <div>
                    <div class="sidebar-logo-text">ExpenseTrack</div>
                    <span class="sidebar-logo-ai">AI</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="sidebar-section">Main</div>
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i data-lucide="layout-dashboard"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('expenses.index') }}" class="sidebar-link {{ request()->routeIs('expenses.*') && !request()->routeIs('expenses.create') ? 'active' : '' }}">
                    <i data-lucide="wallet"></i>
                    <span>Expenses</span>
                </a>
                <a href="{{ route('expenses.create') }}" class="sidebar-link {{ request()->routeIs('expenses.create') ? 'active' : '' }}">
                    <i data-lucide="plus-circle"></i>
                    <span>Add Expense</span>
                </a>

                <div class="sidebar-section">Analytics</div>
                <a href="{{ route('dashboard') }}#reports" class="sidebar-link">
                    <i data-lucide="bar-chart-3"></i>
                    <span>Reports</span>
                </a>
                <a href="{{ route('dashboard') }}#analytics" class="sidebar-link">
                    <i data-lucide="trending-up"></i>
                    <span>Analytics</span>
                </a>
                <a href="#" class="sidebar-link">
                    <i data-lucide="sparkles"></i>
                    <span>AI Insights</span>
                    <span class="badge-premium ai ms-auto">NEW</span>
                </a>

                <div class="sidebar-section">General</div>
                <a href="{{ route('search') }}" class="sidebar-link {{ request()->routeIs('search') ? 'active' : '' }}">
                    <i data-lucide="search"></i>
                    <span>Search</span>
                </a>
                <a href="{{ route('profile.edit') }}" class="sidebar-link">
                    <i data-lucide="settings"></i>
                    <span>Settings</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <div class="sidebar-user" onclick="document.getElementById('logoutForm').submit()" title="Logout">
                    <div class="sidebar-user-avatar">{{ substr(Auth::user()->name, 0, 2) }}</div>
                    <div class="sidebar-user-info">
                        <div class="sidebar-user-name">{{ Auth::user()->name }}</div>
                        <div class="sidebar-user-email">{{ Auth::user()->email }}</div>
                    </div>
                    <form id="logoutForm" method="POST" action="{{ route('logout') }}" class="d-none">@csrf</form>
                    <i data-lucide="log-out" style="width:16px;height:16px;color:var(--text-dim);flex-shrink:0"></i>
                </div>
            </div>
        </aside>

        <div class="main-content">
            <header class="top-nav">
                <div class="top-nav-left">
                    <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Toggle sidebar">
                        <i data-lucide="menu"></i>
                    </button>
                    <div class="top-nav-search">
                        <i data-lucide="search"></i>
                        <input type="text" placeholder="Search expenses..." onfocus="window.location.href='{{ route('expenses.index') }}'">
                    </div>
                </div>
                <div class="top-nav-right">
                    <button class="top-nav-btn" title="Notifications">
                        <i data-lucide="bell"></i>
                        <span class="badge-dot"></span>
                    </button>
                    <div class="dropdown">
                        <button class="top-nav-btn" data-bs-toggle="dropdown" aria-expanded="false">
                            <i data-lucide="sun"></i>
                        </button>
                    </div>
                    <div class="dropdown">
                        <div class="top-nav-avatar" data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer">
                            {{ substr(Auth::user()->name, 0, 2) }}
                        </div>
                        <div class="dropdown-menu dropdown-menu-end dropdown-premium" style="min-width:200px">
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i data-lucide="user"></i> Profile
                            </a>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i data-lucide="settings"></i> Settings
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" onclick="event.preventDefault();document.getElementById('logoutForm').submit()">
                                <i data-lucide="log-out"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <main class="page-content">
                @if (session('success'))
                    <div class="alert-premium alert-success fade-in">
                        <i data-lucide="check-circle" style="width:18px;height:18px;flex-shrink:0"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert-premium alert-error fade-in">
                        <i data-lucide="alert-circle" style="width:18px;height:18px;flex-shrink:0"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    <script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('sidebarBackdrop').classList.toggle('show');
    }

    document.addEventListener('DOMContentLoaded', function () {
        lucide.createIcons();
    });
    </script>

    @stack('scripts')
</body>
</html>