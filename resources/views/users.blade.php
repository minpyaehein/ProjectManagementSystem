@extends('layouts.master')

@section('title', 'Users | Golden Pride')
@section('page-name', 'Users')

@section('content')
    <section class="content">
        <div class="pagehead">
            <div>
                <div class="eyebrow">Directory</div>
                <h1>User Management</h1>
                <p class="sub">Manage users, roles, and account status.</p>
            </div>

            <div class="actions">
                @if (auth()->user()->hasPermission('users.create'))
                    <a class="btn btn-primary" href="{{ route('users.create') }}">
                        ＋ Create user
                    </a>
                @endif
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

        <div class="card">
            <form
                class="toolbar users-toolbar"
                method="GET"
                action="{{ route('usersPage') }}"
            >
                <div class="users-search-group">
                    <input
                        class="search"
                        id="search"
                        name="search"
                        type="search"
                        value="{{ request('search') }}"
                        placeholder="Search users..."
                        aria-label="Search users"
                    >

                    <button class="btn" type="submit">
                        Search
                    </button>

                    @if (request()->filled('search') || request()->filled('status'))
                        <a class="btn users-clear-button" href="{{ route('usersPage') }}">
                            Clear
                        </a>
                    @endif
                </div>

                <select
                    class="filter users-status-filter"
                    id="status"
                    name="status"
                    aria-label="Filter users by status"
                    onchange="this.form.submit()"
                >
                    <option value="">All statuses</option>
                    <option value="Active" @selected(request('status') === 'Active')>
                        Active
                    </option>
                    <option value="Inactive" @selected(request('status') === 'Inactive')>
                        Inactive
                    </option>
                </select>
            </form>

            <div class="table-summary">
                Total users: <strong>{{ $users->total() }}</strong>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Updated</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>
                                    <span class="item-name">{{ $user->name }}</span>
                                    <div class="small-muted">
                                        USR-{{ str_pad((string) $user->user_id, 4, '0', STR_PAD_LEFT) }}
                                    </div>
                                </td>

                                <td>
                                    @if ($user->email)
                                        <a class="user-email-link" href="mailto:{{ $user->email }}">
                                            {{ $user->email }}
                                        </a>
                                    @else
                                        <span class="small-muted">Not provided</span>
                                    @endif
                                </td>

                                <td>{{ $user->role?->name ?? 'No Role' }}</td>

                                <td>
                                    {{ $user->updated_at?->diffForHumans() ?? 'Not available' }}
                                </td>

                                <td>
                                    <span class="pill {{ $user->status === 'Inactive' ? 'gray' : 'green' }}">
                                        {{ $user->status ?? 'Active' }}
                                    </span>
                                </td>

                                <td>
                                    <div class="table-actions">
                                        @if (auth()->user()->hasPermission('users.view'))
                                            <a
                                                class="action-btn action-view"
                                                href="{{ route('users.show', $user) }}"
                                            >
                                                View
                                            </a>
                                        @endif

                                        @if (auth()->user()->hasPermission('users.edit'))
                                            <a
                                                class="action-btn action-edit"
                                                href="{{ route('users.edit', $user) }}"
                                            >
                                                Edit
                                            </a>
                                        @endif

                                        @if (
                                            auth()->user()->hasPermission('users.deactivate')
                                            && auth()->id() !== $user->user_id
                                            && $user->status !== 'Inactive'
                                        )
                                            <form
                                                action="{{ route('users.deactivate', $user) }}"
                                                method="POST"
                                                onsubmit="return confirm('Deactivate this user?');"
                                            >
                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    class="action-btn action-delete"
                                                    type="submit"
                                                >
                                                    Deactivate
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        @if (request()->filled('search') || request()->filled('status'))
                                            <strong>No matching users found.</strong>
                                            <p>Change the search text or selected status.</p>
                                            <a class="btn" href="{{ route('usersPage') }}">
                                                Clear filters
                                            </a>
                                        @else
                                            <strong>No users found.</strong>
                                            <p>Create a user account to display it here.</p>

                                            @if (auth()->user()->hasPermission('users.create'))
                                                <a class="btn btn-primary" href="{{ route('users.create') }}">
                                                    ＋ Create user
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="pagination-wrapper">
                    {{ $users->links('vendor.pagination.custom') }}
                </div>
            @endif
        </div>
    </section>
@endsection


@push('styles')
    <style>
        .users-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .users-search-group {
            display: flex;
            align-items: center;
            gap: 6px;
            flex: 1 1 420px;
            min-width: 0;
        }

        .users-search-group .search {
            flex: 1 1 280px;
            width: auto;
            min-width: 230px;
            max-width: 340px;
            margin: 0;
        }

        .users-search-group .btn {
            flex: 0 0 auto;
            width: auto;
            margin: 0;
            white-space: nowrap;
        }

        .users-status-filter {
            flex: 0 0 180px;
            width: 180px;
            margin-left: auto;
        }

        @media (max-width: 720px) {
            .users-toolbar {
                align-items: stretch;
                flex-direction: column;
            }

            .users-search-group {
                display: grid;
                grid-template-columns: minmax(0, 1fr) auto;
                flex-basis: auto;
                width: 100%;
            }

            .users-search-group .search {
                width: 100%;
                min-width: 0;
                max-width: none;
            }

            .users-search-group .btn {
                width: auto;
            }

            .users-search-group .users-clear-button {
                grid-column: 1 / -1;
                width: 100%;
            }

            .users-status-filter {
                width: 100%;
                margin-left: 0;
            }
        }
    </style>
@endpush
