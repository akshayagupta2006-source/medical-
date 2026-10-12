<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Medical Case Management' }} - Jajal Medical</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <a href="{{ route('dashboard') }}" class="brand">
                <span class="brand-icon">+</span>
                <span>Jajal Medical</span>
            </a>

            @auth
            <div class="nav-links">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                <a href="{{ route('cases.index') }}" class="nav-link {{ request()->routeIs('cases.*') ? 'active' : '' }}">Cases</a>
            </div>

            <div class="user-nav">
                <span class="role-badge {{ auth()->user()->role }}">
                    {{ auth()->user()->role }}
                </span>
                <span style="font-weight: 500; font-size: 0.9rem;">{{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-sm" id="logout-btn">Logout</button>
                </form>
            </div>
            @endauth
        </div>
    </nav>

    <main class="main-container">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                <span>✓</span>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any() && !request()->routeIs('login'))
            <div class="alert alert-danger" role="alert">
                <span>⚠️</span>
                <span>Please correct the errors in the form below.</span>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="footer">
        <p>&copy; {{ date('Y') }} Jajal Medical - Medical Case Management System. All rights reserved.</p>
    </footer>
</body>
</html>
