@extends('layouts.app', ['title' => 'Sign In'])

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div class="brand-icon" style="margin: 0 auto 0.75rem auto; width: 44px; height: 44px; font-size: 1.5rem;">+</div>
            <h1 class="page-title" style="font-size: 1.4rem;">Sign in to your account</h1>
            <p class="page-subtitle">Medical Case Management Portal</p>
        </div>

        @if ($errors->has('email'))
            <div class="alert alert-danger" style="margin-bottom: 1.25rem;">
                <span>⚠️</span>
                <span>{{ $errors->first('email') }}</span>
            </div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST">
            @csrf

            <div class="form-group" style="margin-bottom: 1.15rem;">
                <label for="email" class="form-label">Email Address</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    placeholder="name@jajalmedical.com"
                >
                @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    class="form-control @error('password') is-invalid @enderror"
                    required
                    placeholder="••••••••"
                >
                @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; cursor: pointer;">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span>Remember me</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.7rem;" id="login-submit-btn">
                Sign In
            </button>
        </form>

        <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-color);">
            <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.75rem; text-align: center;">
                Demo Credentials (Click to fill):
            </p>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                <button type="button" class="btn btn-secondary btn-sm" onclick="fillCredentials('admin@jajalmedical.com', 'Password123!')">
                    Admin User
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="fillCredentials('viewer@jajalmedical.com', 'Password123!')">
                    Viewer User
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function fillCredentials(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }
</script>
@endsection
