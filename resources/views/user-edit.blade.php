@extends('layouts.master')

@section('title', 'Edit User | Golden Pride')
@section('page-name', 'Users / Edit')

@section('content')
    <section class="content">
        <div class="pagehead">
            <div>
                <div class="eyebrow">Directory</div>
                <h1>Edit User</h1>
                <p class="sub">Update the user profile, contact information, role, and account status.</p>
            </div>

            <div class="actions">
                <a class="btn" href="{{ route('usersPage') }}">
                    &larr; Back
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="status">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>Please correct the following fields:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <div class="field">
                        <label for="name">Full name</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name', $user->name) }}"
                            maxlength="255"
                            autocomplete="name"
                            autofocus
                            required
                        >
                        @error('name')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="email">Email address</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email', $user->email) }}"
                            maxlength="255"
                            autocomplete="email"
                        >
                        @error('email')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="phone">Phone number</label>
                        <input
                            id="phone"
                            name="phone"
                            type="tel"
                            value="{{ old('phone', $user->phone) }}"
                            maxlength="30"
                            autocomplete="tel"
                            inputmode="tel"
                            placeholder="e.g. 09 123 456 789"
                        >
                        @error('phone')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="role_id">Role</label>
                        <select id="role_id" name="role_id" required>
                            <option value="">Select role</option>
                            @foreach ($roles as $role)
                                <option
                                    value="{{ $role->role_id }}"
                                    @selected(
                                        (string) old('role_id', $user->role_id) ===
                                        (string) $role->role_id
                                    )
                                >
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="password">New password</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="new-password"
                            minlength="8"
                            placeholder="Leave blank to keep current password"
                        >
                        <small class="small-muted">Leave blank unless you want to change the password.</small>
                        @error('password')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="password_confirmation">Confirm new password</label>
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            autocomplete="new-password"
                            minlength="8"
                        >
                    </div>

                    <div class="field">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <option value="Active" @selected(old('status', $user->status) === 'Active')>
                                Active
                            </option>
                            <option value="Inactive" @selected(old('status', $user->status) === 'Inactive')>
                                Inactive
                            </option>
                        </select>
                        @error('status')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-footer">
                    <a class="btn" href="{{ route('usersPage') }}">Cancel</a>
                    <button class="btn btn-primary" type="submit">Save changes</button>
                </div>
            </form>

            @if (
                auth()->user()->hasPermission('users.edit')
                || auth()->user()->hasPermission('users.manage')
            )
                <div class="user-password-reset-section">
                    <div>
                        <span class="user-password-reset-kicker">Account security</span>
                        <h2>Reset password</h2>
                        <p>
                            Reset the account password to
                            <strong>Default@12345</strong>.
                        </p>
                    </div>

                    <form
                        action="{{ route('users.reset-password', $user) }}"
                        method="POST"
                        onsubmit="return confirm('Reset this user password to Default@12345?');"
                    >
                        @csrf
                        @method('PATCH')
                        <button class="btn user-reset-password-button" type="submit">
                            Reset password
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .user-password-reset-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            margin-top: 24px;
            padding: 18px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #f8faf8;
        }

        .user-password-reset-section h2 {
            margin: 0;
            color: var(--ink);
            font-size: 15px;
        }

        .user-password-reset-section p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 11px;
        }

        .user-password-reset-kicker {
            display: block;
            margin-bottom: 5px;
            color: var(--green2);
            font-size: 9px;
            font-weight: 850;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .user-password-reset-section form {
            margin: 0;
        }

        .user-reset-password-button {
            border-color: #e5c5a0;
            background: #fff8ec;
            color: #805817;
        }

        .user-reset-password-button:hover {
            border-color: var(--gold);
            background: #fff1d3;
        }

        @media (max-width: 620px) {
            .user-password-reset-section {
                align-items: stretch;
                flex-direction: column;
            }

            .user-password-reset-section form,
            .user-reset-password-section button {
                width: 100%;
            }
        }
    </style>
@endpush
