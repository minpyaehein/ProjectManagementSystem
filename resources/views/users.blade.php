@extends('layouts.master')

@section('title', 'Users | Golden Pride')
@section('page-name', 'Users')

@section('content')
    <section class="content users-page">
        <div class="pagehead users-pagehead">
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

        <div class="card users-card">
            <form class="toolbar users-toolbar" id="userFilterForm" method="GET" action="{{ route('usersPage') }}">
                <div class="users-search-group">
                    <label class="sr-only" for="userSearch">Search users</label>

                    <input class="search" id="userSearch" name="search" type="search" value="{{ request('search') }}"
                        placeholder="Search users..." autocomplete="off" aria-label="Search users">

                    <button class="btn users-search-button btn-primary" type="submit">
                        Search
                    </button>

                    @if (request()->filled('search') || request()->filled('status'))
                        <a class="btn users-clear-button" href="{{ route('usersPage') }}">
                            Clear
                        </a>
                    @endif
                </div>

                <div class="users-status-group">
                    <label class="sr-only" for="userStatus">
                        Filter users by status
                    </label>

                    <select class="filter users-status-filter" id="userStatus" name="status"
                        aria-label="Filter users by status">
                        <option value="">All statuses</option>
                        <option value="Active" @selected(request('status') === 'Active')>
                            Active
                        </option>
                        <option value="Inactive" @selected(request('status') === 'Inactive')>
                            Inactive
                        </option>
                    </select>
                </div>
            </form>

            <div class="users-filter-progress" id="userFilterProgress" role="status" hidden>
                Updating user results...
            </div>

            <div id="userResults" aria-live="polite">
                <div class="table-summary">
                    Total users: <strong>{{ number_format($users->total()) }}</strong>
                </div>

                <div class="table-wrap users-table-wrap">
                    <table class="users-table">
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
                                                <a class="action-btn action-view" href="{{ route('users.show', $user) }}">
                                                    View
                                                </a>
                                            @endif

                                            @if (auth()->user()->hasPermission('users.edit'))
                                                <a class="action-btn action-edit" href="{{ route('users.edit', $user) }}">
                                                    Edit
                                                </a>
                                            @endif

                                            @if (auth()->user()->hasPermission('users.deactivate') && auth()->id() !== $user->user_id)
                                                @if ($user->status === 'Inactive')
                                                    <form action="{{ route('users.activate', $user) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Activate this user?');"
                                                    >
                                                    @csrf
                                                    @method('PATCH')

                                                    <button class="action-btn action-view" type="submit">
                                                        Activate
                                                    </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('users.deactivate', $user) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Deactivate this user?');"
                                                    >
                                                    @csrf
                                                    @method('PATCH')

                                                    <button class="action-btn action-delete" type="submit">
                                                        Deactivate
                                                    </button>
                                                    </form>
                                                @endif
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
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .users-page,
        .users-card {
            min-width: 0;
        }

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
            flex: 1 1 520px;
            min-width: 0;
        }

        .users-search-group .search {
            flex: 1 1 320px;
            width: 100%;
            min-width: 180px;
            max-width: 280px;
            margin: 0;
        }

        .users-search-group .btn {
            flex: 0 0 auto;
            width: auto;
            margin: 0;
            white-space: nowrap;
        }

        .users-status-group {
            flex: 0 0 auto;
        }

        .users-status-filter {
            width: 180px;
            margin: 0;
        }

        .users-filter-progress {
            margin: -4px 0 12px;
            color: var(--green2);
            font-size: 10px;
            font-weight: 750;
        }

        .users-filter-progress[hidden] {
            display: none;
        }

        #userResults {
            position: relative;
            transition: opacity .15s ease;
        }

        #userResults.is-filtering {
            opacity: .55;
            pointer-events: none;
        }

        .users-table-wrap {
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .users-table {
            min-width: 850px;
        }

        .users-table th:last-child {
            text-align: center;
        }

        .users-table td:last-child {
            text-align: right;
        }

        .user-email-link {
            color: var(--green2);
            font-weight: 650;
        }

        .user-email-link:hover {
            color: var(--green);
            text-decoration: underline;
        }

        .action-delete {
            border-color: #efb0ac;
            background: #fff0ef;
            color: var(--red);
        }

        .table-actions form {
            margin: 0;
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            border: 0;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
        }

        @media (max-width: 720px) {
            .users-page {
                padding-right: 14px;
                padding-left: 14px;
            }

            .users-pagehead {
                align-items: stretch;
                flex-direction: column;
            }

            .users-pagehead .actions,
            .users-pagehead .btn {
                width: 100%;
            }

            .users-card {
                padding: 15px;
            }

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

            .users-status-group,
            .users-status-filter {
                width: 100%;
            }

            .table-summary {
                justify-content: flex-start;
            }
        }

        @media (max-width: 420px) {
            .users-search-group {
                grid-template-columns: 1fr;
            }

            .users-search-group .users-search-button,
            .users-search-group .users-clear-button {
                grid-column: auto;
                width: 100%;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('js/user-auto-filter.js') }}" defer></script>
@endpush
