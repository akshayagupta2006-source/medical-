<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Case Management' }} - Jajal Medical</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="navbar">
        <div class="navbar-container">
            <a href="{{ route('dashboard') }}" class="navbar-brand">
                <span class="brand-badge">Medical</span>
                <span>Jajal Case Management</span>
            </a>

            @auth
            <nav class="nav-menu">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                <a href="{{ route('cases.index') }}" class="nav-link {{ request()->routeIs('cases.*') ? 'active' : '' }}">Cases</a>
            </nav>

            <div class="user-section">
                <span class="role-tag {{ auth()->user()->role }}">
                    {{ auth()->user()->role }}
                </span>
                <span class="user-name">{{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-default btn-sm" id="logout-btn">Logout</button>
                </form>
            </div>
            @endauth
        </div>
    </header>

    <main class="page-container">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                <strong>✓</strong>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any() && !request()->routeIs('login'))
            <div class="alert alert-danger" role="alert">
                <strong>Notice:</strong>
                <span>Please review and correct the errors below before submitting.</span>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="app-footer">
        <p>Jajal Medical &bull; Case Management System &bull; Practical Implementation</p>
    </footer>
</body>
</html>
