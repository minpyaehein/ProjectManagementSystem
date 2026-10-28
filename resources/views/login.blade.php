@extends('layouts.login_master')

@section('title', 'Sign In | Golden Pride')

@section('content')
    <main class="login-page">
        <section class="login-art" aria-labelledby="login-heading">
            <a
                class="brand"
                href="{{ route('login') }}"
                aria-label="Golden Pride sign-in page"
            >
                <img
                    class="logo"
                    src="{{ asset('image/logo.png') }}"
                    alt="Golden Pride"
                >
            </a>

            <div class="login-copy">
                <div class="eyebrow login-art-eyebrow">
                    Projects management, simplified
                </div>

                <h1 id="login-heading">
                    Better projects begin with better decisions.
                </h1>

                <p>
                    Plan projects, coordinate teams, manage clients and
                    suppliers, and monitor organizational activities from one
                    centralized workspace.
                </p>
            </div>

            <div class="login-foot">
                PROJECTS MANAGEMENT SYSTEM
            </div>
        </section>

        <section class="login-side" aria-label="Sign-in form">
            <form
                class="login-box"
                action="{{ route('login-form') }}"
                method="POST"
                novalidate
            >
                @csrf

                <div class="eyebrow">Welcome back</div>

                <h2>Sign in to Projects Management System</h2>

                <p>
                    Enter your account details to access the management
                    workspace.
                </p>

                @if (session('status'))
                    <div class="login-alert login-alert-success" role="status">
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="login-alert login-alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="login-alert login-alert-danger" role="alert">
                        <strong>Sign-in was unsuccessful.</strong>

                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="field">
                    <label for="name">Username</label>

                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        placeholder="Enter your username"
                        maxlength="255"
                        autocomplete="username"
                        aria-describedby="name-error"
                        autofocus
                        required
                    >

                    @error('name')
                        <span class="field-error" id="name-error">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="field">
                    <label for="password">Password</label>

                    <div class="login-password-control">
                        <input
                            id="password"
                            name="password"
                            type="password"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            aria-describedby="password-error"
                            required
                        >

                        <button
                            class="login-password-toggle"
                            id="togglePassword"
                            type="button"
                            aria-controls="password"
                            aria-label="Show password"
                        >
                            Show
                        </button>
                    </div>

                    @error('password')
                        <span class="field-error" id="password-error">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <label class="login-remember" for="remember">
                    <input
                        id="remember"
                        name="remember"
                        type="checkbox"
                        value="1"
                        @checked(old('remember'))
                    >

                    <span>Keep me signed in</span>
                </label>

                <button class="btn btn-primary login-submit" type="submit">
                    Sign in to dashboard →
                </button>
            </form>
        </section>
    </main>
@endsection

@push('styles')
    <style>
        .login-art-eyebrow {
            color: #b8d8c3;
        }

        .login-alert {
            margin: 14px 0;
            padding: 12px 14px;
            border: 1px solid;
            border-radius: 10px;
            font-size: 11px;
            line-height: 1.55;
        }

        .login-alert ul {
            margin: 6px 0 0;
            padding-left: 18px;
        }

        .login-alert-success {
            border-color: #a7d8ba;
            background: #edf9f1;
            color: #17633a;
        }

        .login-alert-danger {
            border-color: #efb0ac;
            background: #fff0ef;
            color: #9e211b;
        }

        .login-password-control {
            position: relative;
        }

        .login-password-control input {
            width: 100%;
            padding-right: 66px;
        }

        .login-password-toggle {
            position: absolute;
            top: 50%;
            right: 8px;
            padding: 5px 9px;
            border: 0;
            border-radius: 6px;
            background: #e8f3ed;
            color: #174f40;
            font-size: 10px;
            font-weight: 800;
            cursor: pointer;
            transform: translateY(-50%);
        }

        .login-password-toggle:hover {
            background: #dcece4;
        }

        .login-password-toggle:focus-visible {
            outline: 2px solid #28735e;
            outline-offset: 2px;
        }

        .login-remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 2px 0 16px;
            color: var(--muted, #68736e);
            font-size: 11px;
            cursor: pointer;
        }

        .login-remember input {
            width: 15px;
            height: 15px;
            margin: 0;
        }

        .login-submit {
            width: 100%;
        }

        .login-box .field-error {
            display: block;
            margin-top: 5px;
            color: #a83a35;
            font-size: 10px;
            font-weight: 700;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.getElementById('togglePassword');

            if (!passwordInput || !toggleButton) {
                return;
            }

            toggleButton.addEventListener('click', function () {
                const shouldShow = passwordInput.type === 'password';

                passwordInput.type = shouldShow ? 'text' : 'password';
                toggleButton.textContent = shouldShow ? 'Hide' : 'Show';
                toggleButton.setAttribute(
                    'aria-label',
                    shouldShow ? 'Hide password' : 'Show password'
                );
            });
        });
    </script>
@endpush
