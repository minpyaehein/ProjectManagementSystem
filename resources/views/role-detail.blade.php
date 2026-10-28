@extends('layouts.master')
@section('title', 'Role Details | Golden Pride')@section('page-name', 'Roles / Details')
@section('content')@php($groups = $role->permissions->groupBy(fn($p) => str($p->slug)->before('.')->toString()))<section class="content">
        <div class="pagehead">
            <div>
                <div class="eyebrow">Access control</div>
                <h1>{{ $role->name }}</h1>
                <p class="sub">Review role information and permissions.</p>
            </div>
            <div class="actions"><a class="btn" href="{{ route('rolesPage') }}">Back</a>
                @if (auth()->user()->hasPermission('roles.edit'))
                    <a class="btn btn-primary" href="{{ route('roles.edit', $role) }}">Edit role</a>
                @endif
            </div>
        </div>
        <div class="role-detail-grid">
            <div class="card role-detail-card">
                <h2>General details</h2>
                <dl class="detail-list">
                    <div>
                        <dt>Role name</dt>
                        <dd>{{ $role->name }}</dd>
                    </div>
                    <div>
                        <dt>Description</dt>
                        <dd>{{ $role->description ?: 'No description' }}</dd>
                    </div>
                    <div>
                        <dt>Assigned users</dt>
                        <dd>{{ $role->users_count }}</dd>
                    </div>
                    <div>
                        <dt>Created</dt>
                        <dd>{{ $role->created_at?->format('M d, Y h:i A') ?? 'Not available' }}</dd>
                    </div>
                    <div>
                        <dt>Updated</dt>
                        <dd>{{ $role->updated_at?->format('M d, Y h:i A') ?? 'Not available' }}</dd>
                    </div>
                </dl>
            </div>
            <div class="card role-detail-card">
                <h2>Assigned permissions</h2>
                @forelse($groups as $module=>$permissions)
                    <section class="detail-permission-group"><strong>{{ str($module)->headline() }}</strong>
                        <div class="detail-permission-list">
                            @foreach ($permissions as $permission)
                                <span
                                    class="detail-permission-pill">{{ str($permission->slug)->afterLast('.')->headline() }}</span>
                            @endforeach
                        </div>
                </section>@empty<p>No permissions assigned.</p>
                @endforelse
            </div>
        </div>
</section>@endsection
