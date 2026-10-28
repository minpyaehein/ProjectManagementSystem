@extends('layouts.master')

@section('title', $user->name . ' | Golden Pride')
@section('page-name', 'Users / View')

@section('content')
    <section class="content user-detail-page">
        <div class="pagehead">
            <div>
                <div class="eyebrow">
                    USR-{{ str_pad((string) $user->user_id, 4, '0', STR_PAD_LEFT) }}
                </div>

                <h1>{{ $user->name }}</h1>

                <p class="sub">
                    View the user profile, assigned role, account status, and inherited permissions.
                </p>
            </div>

            <div class="actions">
                <a class="btn" href="{{ route('usersPage') }}">
                    &larr; Back to users
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

        <div class="user-detail-grid">
            <section class="card user-detail-card">
                <div class="user-detail-heading">
                    <div class="user-detail-avatar" aria-hidden="true">
                        {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                    </div>

                    <div class="user-detail-identity">
                        <h2>{{ $user->name }}</h2>
                        <span>{{ $user->role?->name ?? 'No Role' }}</span>
                    </div>

                    <span class="pill {{ $user->status === 'Inactive' ? 'gray' : 'green' }}">
                        {{ $user->status ?? 'Active' }}
                    </span>
                </div>

                <dl class="user-detail-list">
                    <div>
                        <dt>User ID</dt>
                        <dd>
                            USR-{{ str_pad((string) $user->user_id, 4, '0', STR_PAD_LEFT) }}
                        </dd>
                    </div>

                    <div>
                        <dt>Full name</dt>
                        <dd>{{ $user->name }}</dd>
                    </div>

                    <div>
                        <dt>Email address</dt>
                        <dd>
                            @if ($user->email)
                                <a href="mailto:{{ $user->email }}">
                                    {{ $user->email }}
                                </a>
                            @else
                                Not provided
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt>Phone number</dt>
                        <dd>
                            {{ $user->phone ?: 'Not provided' }}
                        </dd>
                    </div>

                    <div>
                        <dt>Role</dt>
                        <dd>{{ $user->role?->name ?? 'No Role' }}</dd>
                    </div>

                    <div>
                        <dt>Account status</dt>
                        <dd>{{ $user->status ?? 'Active' }}</dd>
                    </div>

                    <div>
                        <dt>Assigned project</dt>
                        <dd>
                            @if ($user->project)
                                @if (auth()->user()->hasPermission('projects.view'))
                                    <a href="{{ route('projects.show', $user->project) }}">
                                        {{ $user->project->name }}
                                    </a>
                                @else
                                    {{ $user->project->name }}
                                @endif
                            @else
                                Not assigned
                            @endif
                        </dd>
                    </div>
                </dl>
            </section>

            <section class="card user-detail-card">
                <div class="card-title">
                    <h2>Record information</h2>
                </div>

                <dl class="user-detail-list">
                    <div>
                        <dt>Created</dt>
                        <dd>
                            {{ $user->created_at?->format('M d, Y h:i A') ?? 'Not available' }}
                        </dd>
                    </div>

                    <div>
                        <dt>Last updated</dt>
                        <dd>
                            {{ $user->updated_at?->format('M d, Y h:i A') ?? 'Not available' }}
                        </dd>
                    </div>

                    <div>
                        <dt>Last updated relative</dt>
                        <dd>
                            {{ $user->updated_at?->diffForHumans() ?? 'Not available' }}
                        </dd>
                    </div>
                </dl>

                <div class="user-detail-actions">
                    @if (auth()->user()->hasPermission('users.edit'))
                        <a class="btn btn-soft" href="{{ route('users.edit', $user) }}">
                            Edit user information
                        </a>
                    @endif

                    @if (auth()->user()->hasPermission('users.deactivate') &&
                            auth()->id() !== $user->user_id &&
                            $user->status !== 'Inactive')
                        <form action="{{ route('users.deactivate', $user) }}" method="POST"
                            onsubmit="return confirm('Deactivate this user?');">
                            @csrf
                            @method('PATCH')

                            <button class="btn user-detail-deactivate" type="submit">
                                Deactivate user
                            </button>
                        </form>
                    @endif
                </div>
            </section>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .user-detail-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(280px, .85fr);
            gap: 18px;
        }

        .user-detail-card {
            min-width: 0;
        }

        .user-detail-heading {
            display: flex;
            align-items: center;
            gap: 14px;
            padding-bottom: 18px;
            border-bottom: 1px solid var(--line);
        }

        .user-detail-avatar {
            display: grid;
            flex: 0 0 52px;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: var(--green);
            color: var(--white);
            font-size: 19px;
            font-weight: 850;
            place-items: center;
        }

        .user-detail-identity {
            min-width: 0;
            flex: 1;
        }

        .user-detail-identity h2 {
            margin: 0 0 4px;
            color: var(--ink);
            font-size: 17px;
        }

        .user-detail-identity span {
            color: var(--muted);
            font-size: 11px;
        }

        .user-detail-list {
            margin: 0;
        }

        .user-detail-list>div {
            display: grid;
            grid-template-columns: 150px minmax(0, 1fr);
            gap: 18px;
            padding: 13px 0;
            border-bottom: 1px solid #edf0ec;
        }

        .user-detail-list>div:last-child {
            border-bottom: 0;
        }

        .user-detail-list dt {
            color: var(--muted);
            font-size: 10px;
            font-weight: 750;
        }

        .user-detail-list dd {
            margin: 0;
            color: var(--ink);
            font-size: 11px;
            font-weight: 750;
            overflow-wrap: anywhere;
        }

        .user-detail-list dd a {
            color: var(--green2);
        }

        .user-detail-list dd a:hover {
            text-decoration: underline;
        }

        .user-detail-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 20px;
            padding-top: 18px;
            border-top: 1px solid var(--line);
        }

        .user-detail-actions form {
            margin: 0;
        }

        .user-detail-deactivate {
            border-color: #efb0ac;
            background: #fff0ef;
            color: var(--red);
        }

        .user-detail-deactivate:hover {
            border-color: var(--red);
            background: #fde7e5;
        }

        .user-permission-card {
            grid-column: 1 / -1;
        }

        .user-permission-heading {
            align-items: flex-start;
        }

        .user-permission-list {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .user-permission-item {
            min-width: 0;
            padding: 12px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #fafcfb;
        }

        .user-permission-item strong {
            display: block;
            margin-bottom: 5px;
            color: var(--ink);
            font-size: 11px;
        }

        .user-permission-item span {
            display: block;
            color: var(--green2);
            font-family: ui-monospace, SFMono-Regular, Consolas, monospace;
            font-size: 9px;
            overflow-wrap: anywhere;
        }

        @media (max-width: 900px) {
            .user-detail-grid {
                grid-template-columns: 1fr;
            }

            .user-permission-card {
                grid-column: auto;
            }

            .user-permission-list {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 620px) {
            .user-detail-heading {
                align-items: flex-start;
                flex-wrap: wrap;
            }

            .user-detail-list>div {
                grid-template-columns: 1fr;
                gap: 5px;
            }

            .user-permission-list {
                grid-template-columns: 1fr;
            }

            .user-detail-actions {
                flex-direction: column;
            }

            .user-detail-actions .btn,
            .user-detail-actions form,
            .user-detail-actions button {
                width: 100%;
            }
        }
    </style>
@endpush
