@extends('layouts.master')

@section('title', 'Create User | Golden Pride')
@section('page-name', 'Users / Create')

@section('content')
    <section class="content">
        <div class="pagehead">
            <div>
                <div class="eyebrow">Directory</div>
                <h1>Create User</h1>
                <p class="sub">Create a user account and assign the appropriate role.</p>
            </div>

            <div class="actions">
                <a class="btn" href="{{ route('usersPage') }}">
                    &larr; Back
                </a>
            </div>
        </div>

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
            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="form-grid">
                    <div class="field">
                        <label for="name">Full name</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name') }}"
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
                            value="{{ old('email') }}"
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
                            value="{{ old('phone') }}"
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
                                        (string) old('role_id') ===
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
                        <label for="password">Temporary password</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="new-password"
                            required
                        >

                        @error('password')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="password_confirmation">Confirm password</label>
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            autocomplete="new-password"
                            required
                        >
                    </div>

                    <div class="field">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <option
                                value="Active"
                                @selected(old('status', 'Active') === 'Active')
                            >
                                Active
                            </option>
                            <option
                                value="Inactive"
                                @selected(old('status') === 'Inactive')
                            >
                                Inactive
                            </option>
                        </select>

                        @error('status')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-footer">
                    <a class="btn" href="{{ route('usersPage') }}">
                        Cancel
                    </a>
                    <button class="btn btn-primary" type="submit">
                        Save record
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection
