@extends('layouts.master')
@section('title', 'Roles | Golden Pride')@section('page-name', 'Roles')
@section('content')<section class="content">
        <div class="pagehead">
            <div>
                <div class="eyebrow">Access control</div>
                <h1>Roles</h1>
                <p class="sub">Manage roles and system access.</p>
            </div>
            <div class="actions">
                @if (auth()->user()->hasPermission('roles.create'))
                    <a class="btn btn-primary" href="{{ route('roles.create') }}">＋ Create role</a>
                @endif
            </div>
        </div>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="card">
            <form class="toolbar" style="justify-content: normal" method="GET" action="{{ route('rolesPage') }}"><input class="search" name="search"
                    value="{{ request('search') }}" placeholder="Search roles..."><button class="btn">Search</button>
            </form>
            <div class="table-summary">Total roles: <strong>{{ $roles->total() }}</strong></div>
            <div class="table-wrap">
                <table>
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
                        @forelse($roles as $role)
                            <tr>
                                <td><span class="item-name">{{ $role->name }}</span>
                                    <div class="small-muted">ROL-{{ str_pad($role->role_id, 4, '0', STR_PAD_LEFT) }}</div>
                                </td>
                                <td>{{ $role->description ?: 'No description' }}</td>
                                <td>{{ $role->users_count }}</td>
                                <td>{{ $role->created_at?->format('M d, Y') ?? 'Not available' }}</td>
                                <td>{{ $role->updated_at?->format('M d, Y') ?? 'Not available' }}</td>
                                <td>
                                    <div class="table-actions">
                                        @if (auth()->user()->hasPermission('roles.view'))
                                            <a class="action-btn action-view"
                                                href="{{ route('roles.show', $role) }}">View</a>
                                            @endif @if (auth()->user()->hasPermission('roles.edit'))
                                                <a class="action-btn action-edit"
                                                    href="{{ route('roles.edit', $role) }}">Edit</a>
                                                @endif @if (auth()->user()->hasPermission('roles.deactivate') &&
                                                        (int) $role->users_count === 0 &&
                                                        ($role->status ?? 'Active') !== 'Inactive')
                                                    <form action="{{ route('roles.deactivate', $role) }}" method="POST">
                                                        @csrf @method('PATCH')<button
                                                            class="action-btn action-delete">Deactivate</button></form>
                                                @endif
                                    </div>
                                </td>
                        </tr>@empty<tr>
                                <td colspan="6">No roles found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
</section>@endsection
