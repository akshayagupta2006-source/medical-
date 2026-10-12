@extends('layouts.app', ['title' => 'Sign In'])

@section('content')
<div class="auth-container">
    <div class="auth-box">
        <div class="auth-header">
            <span class="brand-badge" style="margin-bottom: 0.5rem; display: inline-block;">Jajal Medical</span>
            <h1>Case Management Portal</h1>
            <p>Please enter your credentials to access the system</p>
        </div>

        @if ($errors->has('email'))
            <div class="alert alert-danger" style="margin-bottom: 1rem;">
                <span>{{ $errors->first('email') }}</span>
            </div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST">
            @csrf

            <div class="form-group" style="margin-bottom: 0.85rem;">
                <label for="email" class="form-label">Email Address</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    class="form-control @error('email') has-error @enderror"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    placeholder="name@jajalmedical.com"
                >
                @error('email')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    class="form-control @error('password') has-error @enderror"
                    required
                    placeholder="••••••••"
                >
                @error('password')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 1.15rem; display: flex; align-items: center; justify-content: space-between;">
                <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.825rem; color: var(--text-secondary); cursor: pointer;">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span>Keep me signed in</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.55rem;" id="login-submit-btn">
                Sign In
            </button>
        </form>

        <div class="demo-credentials">
            <div class="demo-title">Test Accounts (Click to auto-fill)</div>
            <div class="demo-buttons">
                <button type="button" class="btn btn-default btn-sm" onclick="fillCredentials('admin@jajalmedical.com', 'Password123!')">
                    Admin User
                </button>
                <button type="button" class="btn btn-default btn-sm" onclick="fillCredentials('viewer@jajalmedical.com', 'Password123!')">
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
