@extends('layouts.master')

@section('title', 'Roles | Golden Pride')
@section('page-name', 'Roles')

@section('content')
    <section class="content roles-page">
        <div class="pagehead roles-pagehead">
            <div>
                <div class="eyebrow">Access control</div>
                <h1>Roles</h1>
                <p class="sub">Manage roles and system access.</p>
            </div>

            <div class="actions">
                @if (auth()->user()->hasPermission('roles.create'))
                    <a class="btn btn-primary" href="{{ route('roles.create') }}">
                        ＋ Create role
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

        <div class="card roles-card">
            <form class="toolbar roles-toolbar" id="roleFilterForm" method="GET" action="{{ route('rolesPage') }}">
                <div class="role-search-group">
                    <label class="sr-only" for="roleSearch">Search roles</label>

                    <input class="search" id="roleSearch" name="search" type="search" value="{{ request('search') }}"
                        placeholder="Search roles..." autocomplete="off" aria-label="Search roles">

                    <button class="btn role-search-button btn-primary" type="submit">
                        Search
                    </button>

                    @if (request()->filled('search'))
                        <a class="btn role-clear-button" href="{{ route('rolesPage') }}">
                            Clear
                        </a>
                    @endif
                </div>
            </form>

            <div class="role-filter-progress" id="roleFilterProgress" role="status" hidden>
                Updating role results...
            </div>

            <div id="roleResults" aria-live="polite">
                <div class="table-summary">
                    Total roles: <strong>{{ number_format($roles->total()) }}</strong>
                </div>

                <div class="table-wrap roles-table-wrap">
                    <table class="roles-table">
                        <thead>
                            <tr>
                                <th>Role</th>
                                <th>Description</th>
                                <th>Users</th>
                                <th>Created</th>
                                <th>Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($roles as $role)
                                <tr>
                                    <td>
                                        <span class="item-name">{{ $role->name }}</span>
                                        <div class="small-muted">
                                            ROL-{{ str_pad((string) $role->role_id, 4, '0', STR_PAD_LEFT) }}
                                        </div>
                                    </td>

                                    <td>
                                        <span class="role-description" title="{{ $role->description ?: 'No description' }}">
                                            {{ $role->description ?: 'No description' }}
                                        </span>
                                    </td>

                                    <td>{{ number_format($role->users_count) }}</td>

                                    <td>
                                        {{ $role->created_at?->format('M d, Y') ?? 'Not available' }}
                                    </td>

                                    <td>
                                        {{ $role->updated_at?->format('M d, Y') ?? 'Not available' }}
                                    </td>

                                    <td>
                                        <div class="table-actions">
                                            @if (auth()->user()->hasPermission('roles.view'))
                                                <a class="action-btn action-view" href="{{ route('roles.show', $role) }}">
                                                    View
                                                </a>
                                            @endif

                                            @if (auth()->user()->hasPermission('roles.edit'))
                                                <a class="action-btn action-edit" href="{{ route('roles.edit', $role) }}">
                                                    Edit
                                                </a>
                                            @endif

                                            @if (auth()->user()->hasPermission('roles.deactivate'))
                                                @if (($role->status ?? 'Active') === 'Inactive')
                                                    <form action="{{ route('roles.activate', $role) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Activate this role?');"
                                                    >
                                                    @csrf
                                                    @method('PATCH')

                                                    <button class="action-btn action-view" type="submit">
                                                        Activate
                                                    </button>
                                                    </form>
                                                @elseif ((int) $role->users_count === 0 && (int) auth()->user()->role_id !== (int) $role->role_id)
                                                    <form action="{{ route('roles.deactivate', $role) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Deactivate this role?');"
                                                    >
                                                    @csrf
                                                    @method('PATCH')

                                                    <button class="action-btn action-delete" type="submit">
                                                        Deactivate
                                                    </button>
                                                    </form>
                                                @elseif ((int) $role->users_count > 0)
                                                    <span class="action-btn action-disabled"
                                                        title="Roles assigned to users cannot be deactivated."
                                                        aria-disabled="true">
                                                        In use
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state">
                                            @if (request()->filled('search'))
                                                <strong>No matching roles found.</strong>
                                                <p>Change or clear the search text.</p>
                                                <a class="btn" href="{{ route('rolesPage') }}">
                                                    Clear search
                                                </a>
                                            @else
                                                <strong>No roles found.</strong>
                                                <p>Create a role to display it here.</p>

                                                @if (auth()->user()->hasPermission('roles.create'))
                                                    <a class="btn btn-primary" href="{{ route('roles.create') }}">
                                                        ＋ Create role
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

                @if ($roles->hasPages())
                    <div class="pagination-wrapper">
                        {{ $roles->links('vendor.pagination.custom') }}
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .roles-page,
        .roles-card {
            min-width: 0;
        }

        .roles-toolbar {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 12px;
            margin-bottom: 16px;
        }

        .role-search-group {
            display: flex;
            align-items: center;
            gap: 6px;
            flex: 1 1 520px;
            min-width: 0;
        }

        .role-search-group .search {
            flex: 1 1 320px;
            width: 100%;
            min-width: 180px;
            max-width: 280px;
            margin: 0;
        }

        .role-search-group .btn {
            flex: 0 0 auto;
            width: auto;
            margin: 0;
            white-space: nowrap;
        }

        .role-filter-progress {
            margin: -4px 0 12px;
            color: var(--green2);
            font-size: 10px;
            font-weight: 750;
        }

        .role-filter-progress[hidden] {
            display: none;
        }

        #roleResults {
            position: relative;
            transition: opacity .15s ease;
        }

        #roleResults.is-filtering {
            opacity: .55;
            pointer-events: none;
        }

        .roles-table-wrap {
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .roles-table {
            min-width: 920px;
        }

        .roles-table th:last-child {
            text-align: center;
        }

        .roles-table td:last-child {
            text-align: right;
        }

        .role-description {
            display: inline-block;
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: middle;
            white-space: nowrap;
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
            .roles-page {
                padding-right: 14px;
                padding-left: 14px;
            }

            .roles-pagehead {
                align-items: stretch;
                flex-direction: column;
            }

            .roles-pagehead .actions,
            .roles-pagehead .btn {
                width: 100%;
            }

            .roles-card {
                padding: 15px;
            }

            .roles-toolbar {
                align-items: stretch;
                flex-direction: column;
            }

            .role-search-group {
                display: grid;
                grid-template-columns: minmax(0, 1fr) auto;
                flex-basis: auto;
                width: 100%;
            }

            .role-search-group .search {
                width: 100%;
                min-width: 0;
                max-width: none;
            }

            .role-search-group .btn {
                width: auto;
            }

            .role-search-group .role-clear-button {
                grid-column: 1 / -1;
                width: 100%;
            }

            .table-summary {
                justify-content: flex-start;
            }
        }

        @media (max-width: 420px) {
            .role-search-group {
                grid-template-columns: 1fr;
            }

            .role-search-group .role-search-button,
            .role-search-group .role-clear-button {
                grid-column: auto;
                width: 100%;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('js/role-auto-filter.js') }}" defer></script>
@endpush
