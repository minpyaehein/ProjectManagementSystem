@extends('layouts.master')

@section('title', 'My Profile | Golden Pride')
@section('page-name', 'My Profile')

@section('content')
    <section class="content profile-page">
        <div class="pagehead">
            <div>
                <div class="eyebrow">My account</div>
                <h1>Profile</h1>
                <p class="sub">Update your personal information and account password.</p>
            </div>

            <div class="actions">
                <a class="btn" href="{{ route('homePage') }}">
                    &larr; Back to dashboard
                </a>
            </div>
        </div>

        <div class="profile-layout">
            <aside class="card profile-summary-card">
                <div class="profile-avatar-large" aria-hidden="true">
                    {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                </div>

                <h2>{{ $user->name }}</h2>
                <p>{{ $user->role?->name ?? 'No Role' }}</p>

                <span class="pill {{ $user->status === 'Inactive' ? 'gray' : 'green' }}">
                    {{ $user->status ?? 'Active' }}
                </span>

                <dl class="profile-summary-list">
                    <div>
                        <dt>User ID</dt>
                        <dd>USR-{{ str_pad((string) $user->user_id, 4, '0', STR_PAD_LEFT) }}</dd>
                    </div>
                    <div>
                        <dt>Email</dt>
                        <dd>{{ $user->email ?: 'Not provided' }}</dd>
                    </div>
                    <div>
                        <dt>Phone</dt>
                        <dd>{{ $user->phone ?: 'Not provided' }}</dd>
                    </div>
                </dl>
            </aside>

            <div class="profile-main-column">
                <section class="card profile-form-card">
                    <div class="profile-section-heading">
                        <div>
                            <span class="profile-section-kicker">Personal details</span>
                            <h2>Profile information</h2>
                            <p>Changes to role and status can only be made by an authorized administrator.</p>
                        </div>
                    </div>

                    @if (session('profile_success'))
                        <div class="alert alert-success" role="status">
                            {{ session('profile_success') }}
                        </div>
                    @endif

                    @if ($errors->profileUpdate->any())
                        <div class="alert alert-danger" role="alert">
                            <ul>
                                @foreach ($errors->profileUpdate->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-grid">
                            <div class="field">
                                <label for="profile_name">Full name</label>
                                <input
                                    id="profile_name"
                                    name="name"
                                    type="text"
                                    value="{{ old('name', $user->name) }}"
                                    maxlength="255"
                                    autocomplete="name"
                                    required
                                >
                                @error('name', 'profileUpdate')
                                    <span class="field-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="field">
                                <label for="profile_email">Email address</label>
                                <input
                                    id="profile_email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email', $user->email) }}"
                                    maxlength="255"
                                    autocomplete="email"
                                >
                                @error('email', 'profileUpdate')
                                    <span class="field-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="field">
                                <label for="profile_phone">Phone number</label>
                                <input
                                    id="profile_phone"
                                    name="phone"
                                    type="tel"
                                    value="{{ old('phone', $user->phone) }}"
                                    maxlength="30"
                                    autocomplete="tel"
                                    inputmode="tel"
                                    placeholder="e.g. 09 123 456 789"
                                >
                                @error('phone', 'profileUpdate')
                                    <span class="field-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="field">
                                <label>Role</label>
                                <input
                                    type="text"
                                    value="{{ $user->role?->name ?? 'No Role' }}"
                                    disabled
                                >
                            </div>
                        </div>

                        <div class="form-footer">
                            <button class="btn btn-primary" type="submit">
                                Save profile
                            </button>
                        </div>
                    </form>
                </section>

                <section class="card profile-form-card">
                    <div class="profile-section-heading">
                        <div>
                            <span class="profile-section-kicker">Account security</span>
                            <h2>Change password</h2>
                            <p>Enter your current password before creating a new password.</p>
                        </div>
                    </div>

                    @if (session('password_success'))
                        <div class="alert alert-success" role="status">
                            {{ session('password_success') }}
                        </div>
                    @endif

                    @if ($errors->passwordUpdate->any())
                        <div class="alert alert-danger" role="alert">
                            <ul>
                                @foreach ($errors->passwordUpdate->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('profile.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-grid">
                            <div class="field full">
                                <label for="current_password">Current password</label>
                                <div class="profile-password-control">
                                    <input
                                        id="current_password"
                                        name="current_password"
                                        type="password"
                                        autocomplete="current-password"
                                        required
                                    >
                                    <button
                                        class="profile-password-toggle"
                                        type="button"
                                        data-password-toggle="current_password"
                                        aria-label="Show current password"
                                    >
                                        Show
                                    </button>
                                </div>
                                @error('current_password', 'passwordUpdate')
                                    <span class="field-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="field">
                                <label for="password">New password</label>
                                <div class="profile-password-control">
                                    <input
                                        id="password"
                                        name="password"
                                        type="password"
                                        minlength="8"
                                        autocomplete="new-password"
                                        required
                                    >
                                    <button
                                        class="profile-password-toggle"
                                        type="button"
                                        data-password-toggle="password"
                                        aria-label="Show new password"
                                    >
                                        Show
                                    </button>
                                </div>
                                @error('password', 'passwordUpdate')
                                    <span class="field-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="field">
                                <label for="password_confirmation">Confirm new password</label>
                                <div class="profile-password-control">
                                    <input
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        type="password"
                                        minlength="8"
                                        autocomplete="new-password"
                                        required
                                    >
                                    <button
                                        class="profile-password-toggle"
                                        type="button"
                                        data-password-toggle="password_confirmation"
                                        aria-label="Show password confirmation"
                                    >
                                        Show
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-footer">
                            <button class="btn btn-primary" type="submit">
                                Change password
                            </button>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .profile-layout {
            display: grid;
            grid-template-columns: minmax(230px, .7fr) minmax(0, 2fr);
            align-items: start;
            gap: 18px;
        }

        .profile-summary-card {
            position: sticky;
            top: 76px;
            text-align: center;
        }

        .profile-avatar-large {
            display: grid;
            width: 76px;
            height: 76px;
            margin: 0 auto 14px;
            border-radius: 50%;
            background: var(--green);
            color: var(--white);
            font-size: 28px;
            font-weight: 850;
            place-items: center;
        }

        .profile-summary-card h2 {
            margin: 0;
            color: var(--ink);
            font-size: 18px;
        }

        .profile-summary-card > p {
            margin: 5px 0 12px;
            color: var(--muted);
            font-size: 11px;
        }

        .profile-summary-list {
            margin: 20px 0 0;
            text-align: left;
        }

        .profile-summary-list > div {
            padding: 11px 0;
            border-top: 1px solid var(--line);
        }

        .profile-summary-list dt {
            margin-bottom: 4px;
            color: var(--muted);
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .profile-summary-list dd {
            margin: 0;
            color: var(--ink);
            font-size: 11px;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .profile-main-column {
            display: grid;
            gap: 18px;
        }

        .profile-section-heading {
            margin-bottom: 18px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--line);
        }

        .profile-section-kicker {
            color: var(--green2);
            font-size: 9px;
            font-weight: 850;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .profile-section-heading h2 {
            margin: 4px 0 0;
            color: var(--ink);
            font-size: 16px;
        }

        .profile-section-heading p {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 11px;
        }

        .profile-password-control {
            position: relative;
        }

        .profile-password-control input {
            padding-right: 62px;
        }

        .profile-password-toggle {
            position: absolute;
            top: 50%;
            right: 7px;
            padding: 5px 8px;
            border: 0;
            border-radius: 6px;
            background: var(--mint);
            color: var(--green);
            font-size: 9px;
            font-weight: 800;
            cursor: pointer;
            transform: translateY(-50%);
        }

        @media (max-width: 850px) {
            .profile-layout {
                grid-template-columns: 1fr;
            }

            .profile-summary-card {
                position: static;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
                button.addEventListener('click', function () {
                    const input = document.getElementById(button.dataset.passwordToggle);
                    if (!input) return;

                    const showPassword = input.type === 'password';
                    input.type = showPassword ? 'text' : 'password';
                    button.textContent = showPassword ? 'Hide' : 'Show';
                    button.setAttribute(
                        'aria-label',
                        (showPassword ? 'Hide' : 'Show') + ' password'
                    );
                });
            });
        });
    </script>
@endpush
