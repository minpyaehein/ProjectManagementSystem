@extends('layouts.master')

@section('title', 'Dashboard | Golden Pride')
@section('page-name', 'Dashboard')

@section('content')
    @php
        $currentUser = auth()->user();
        $hour = (int) now()->format('H');

        $greeting = match (true) {
            $hour < 12 => 'Good morning',
            $hour < 18 => 'Good afternoon',
            default => 'Good evening',
        };

        $statusClass = static function (?string $status): string {
            return match (strtolower(trim((string) $status))) {
                'completed', 'payment' => 'dashboard-status-success',
                'cancelled', 'lost' => 'dashboard-status-danger',
                'proposal', 'negotiation', 'quotation' => 'dashboard-status-warning',
                default => 'dashboard-status-info',
            };
        };
    @endphp

    <section class="content dashboard-page">
        <div class="pagehead dashboard-pagehead">
            <div>
                <div class="eyebrow">Overview</div>

                <h1>{{ $greeting }}, {{ $currentUser->name }}.</h1>

                <p class="sub">
                    Here is the latest overview of your project management workspace.
                </p>
            </div>


        </div>

        <div class="dashboard-stat-grid">
            <a
                class="card dashboard-stat-card"
                href="{{ $currentUser->hasPermission('projects.view') ? route('projectsPage', ['record' => 'active']) : '#' }}"
            >
                <div class="dashboard-stat-top">
                    <span>Active projects</span>
                    <span class="dashboard-stat-icon" aria-hidden="true">▣</span>
                </div>
                <strong class="dashboard-stat-value">{{ number_format($activeProjectCount) }}</strong>
                <span class="dashboard-stat-note">Currently active project records</span>
            </a>

            <a
                class="card dashboard-stat-card"
                href="{{ $currentUser->hasPermission('customers.view') ? route('customersPage') : '#' }}"
            >
                <div class="dashboard-stat-top">
                    <span>Customers</span>
                    <span class="dashboard-stat-icon" aria-hidden="true">♧</span>
                </div>
                <strong class="dashboard-stat-value">{{ number_format($customerCount) }}</strong>
                <span class="dashboard-stat-note">Total customer records</span>
            </a>

            <a
                class="card dashboard-stat-card"
                href="{{ $currentUser->hasPermission('suppliers.view') ? route('suppliersPage') : '#' }}"
            >
                <div class="dashboard-stat-top">
                    <span>Suppliers</span>
                    <span class="dashboard-stat-icon" aria-hidden="true">◇</span>
                </div>
                <strong class="dashboard-stat-value">{{ number_format($supplierCount) }}</strong>
                <span class="dashboard-stat-note">Registered supplier partners</span>
            </a>

            <a
                class="card dashboard-stat-card"
                href="{{ $currentUser->hasPermission('users.view') ? route('usersPage', ['status' => 'Active']) : '#' }}"
            >
                <div class="dashboard-stat-top">
                    <span>Active users</span>
                    <span class="dashboard-stat-icon" aria-hidden="true">♙</span>
                </div>
                <strong class="dashboard-stat-value">{{ number_format($activeUserCount) }}</strong>
                <span class="dashboard-stat-note">
                    Across {{ number_format($roleCount) }} {{ $roleCount === 1 ? 'role' : 'roles' }}
                </span>
            </a>
        </div>

        <div class="dashboard-content-grid">
            <section class="card dashboard-projects-card">
                <div class="card-title dashboard-card-title">
                    <div>
                        <h2>Recent projects</h2>
                        <p>Latest project records in the workspace.</p>
                    </div>

                    @if ($currentUser->hasPermission('projects.view'))
                        <a href="{{ route('projectsPage') }}">View all →</a>
                    @endif
                </div>

                <div class="table-wrap">
                    <table class="dashboard-project-table">
                        <thead>
                            <tr>
                                <th>Project</th>
                                <th>End User</th>
                                <th>Account Manager</th>
                                <th>End Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($recentProjects as $project)
                                <tr>
                                    <td>
                                        @if ($currentUser->hasPermission('projects.view'))
                                            <a
                                                class="item-name dashboard-project-link"
                                                href="{{ route('projects.show', $project) }}"
                                            >
                                                {{ $project->name }}
                                            </a>
                                        @else
                                            <span class="item-name">{{ $project->name }}</span>
                                        @endif

                                        <div class="small-muted">
                                            PRJ-{{ str_pad((string) $project->project_id, 4, '0', STR_PAD_LEFT) }}
                                        </div>
                                    </td>

                                    <td>{{ $project->customer?->name ?? 'Not provided' }}</td>
                                    <td>{{ $project->accountManagerName ?: 'Not provided' }}</td>
                                    <td>{{ $project->end_date?->format('M d, Y') ?? 'No deadline' }}</td>

                                    <td>
                                        <span class="dashboard-status {{ $statusClass($project->status) }}">
                                            {{ $project->status ?: 'Not set' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="dashboard-empty-state">
                                            <strong>No projects found.</strong>

                                            @if ($currentUser->hasPermission('projects.create'))
                                                <a class="btn btn-primary" href="{{ route('project-create') }}">
                                                    ＋ Create project
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <aside class="dashboard-side-column">
                <section class="card dashboard-directory-card">
                    <div class="card-title dashboard-card-title">
                        <div>
                            <h2>Recently added</h2>
                            <p>Latest customer and supplier records.</p>
                        </div>
                    </div>

                    <div class="dashboard-directory-section">
                        <div class="dashboard-directory-heading">
                            <strong>Customers</strong>

                            @if ($currentUser->hasPermission('customers.view'))
                                <a href="{{ route('customersPage') }}">View all</a>
                            @endif
                        </div>

                        @forelse ($recentCustomers as $customer)
                            <div class="dashboard-directory-item">
                                <span class="dashboard-directory-avatar" aria-hidden="true">
                                    {{ strtoupper(mb_substr($customer->name, 0, 1)) }}
                                </span>

                                <div>
                                    <strong>{{ $customer->name }}</strong>
                                    <span>{{ $customer->contact_person ?: 'No primary contact' }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="small-muted">No customer records found.</p>
                        @endforelse
                    </div>

                    <div class="dashboard-directory-section">
                        <div class="dashboard-directory-heading">
                            <strong>Suppliers</strong>

                            @if ($currentUser->hasPermission('suppliers.view'))
                                <a href="{{ route('suppliersPage') }}">View all</a>
                            @endif
                        </div>

                        @forelse ($recentSuppliers as $supplier)
                            <div class="dashboard-directory-item">
                                <span class="dashboard-directory-avatar supplier" aria-hidden="true">
                                    {{ strtoupper(mb_substr($supplier->name, 0, 1)) }}
                                </span>

                                <div>
                                    <strong>{{ $supplier->name }}</strong>
                                    <span>{{ $supplier->category ?: 'No category' }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="small-muted">No supplier records found.</p>
                        @endforelse
                    </div>
                </section>

                <section class="card dashboard-project-summary-card">
                    <div class="dashboard-project-summary-heading">
                        <div>
                            <span class="dashboard-summary-kicker">Project records</span>
                            <h2>Status summary</h2>
                            <p>Current Active and Inactive project records.</p>
                        </div>

                        @if ($currentUser->hasPermission('projects.view'))
                            <a href="{{ route('projectsPage') }}">View all</a>
                        @endif
                    </div>

                    <div class="dashboard-project-summary-grid">
                        @if ($currentUser->hasPermission('projects.view'))
                            <a
                                class="dashboard-project-summary-item summary-active"
                                href="{{ route('projectsPage', ['record' => 'active']) }}"
                            >
                                <span class="dashboard-summary-icon" aria-hidden="true">✓</span>
                                <div>
                                    <strong>{{ number_format($activeProjectCount) }}</strong>
                                    <small>Active</small>
                                </div>
                            </a>

                            <a
                                class="dashboard-project-summary-item summary-inactive"
                                href="{{ route('projectsPage', ['record' => 'inactive']) }}"
                            >
                                <span class="dashboard-summary-icon" aria-hidden="true">−</span>
                                <div>
                                    <strong>{{ number_format($inactiveProjectCount) }}</strong>
                                    <small>Inactive</small>
                                </div>
                            </a>
                        @else
                            <div class="dashboard-project-summary-item summary-active">
                                <span class="dashboard-summary-icon" aria-hidden="true">✓</span>
                                <div>
                                    <strong>{{ number_format($activeProjectCount) }}</strong>
                                    <small>Active</small>
                                </div>
                            </div>

                            <div class="dashboard-project-summary-item summary-inactive">
                                <span class="dashboard-summary-icon" aria-hidden="true">−</span>
                                <div>
                                    <strong>{{ number_format($inactiveProjectCount) }}</strong>
                                    <small>Inactive</small>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="dashboard-project-summary-total">
                        <span>Total project records</span>
                        <strong>{{ number_format($totalProjectCount) }}</strong>
                    </div>
                </section>
            </aside>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .dashboard-stat-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }

        .dashboard-stat-card {
            display: block;
            color: inherit;
            text-decoration: none;
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        }

        .dashboard-stat-card[href="#"] {
            cursor: default;
            pointer-events: none;
        }

        .dashboard-stat-card:hover {
            border-color: #bdd5c9;
            box-shadow: 0 14px 34px rgba(21, 54, 43, .11);
            transform: translateY(-2px);
        }

        .dashboard-stat-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            color: var(--muted);
            font-size: 10px;
            font-weight: 750;
        }

        .dashboard-stat-icon {
            display: grid;
            width: 30px;
            height: 30px;
            border-radius: 9px;
            background: var(--mint);
            color: var(--green);
            font-size: 15px;
            place-items: center;
        }

        .dashboard-stat-value {
            display: block;
            margin: 13px 0 6px;
            color: var(--ink);
            font-size: 26px;
            line-height: 1;
        }

        .dashboard-stat-note {
            color: var(--green2);
            font-size: 9px;
            font-weight: 700;
        }

        .dashboard-content-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.65fr) minmax(280px, .75fr);
            align-items: start;
            gap: 18px;
        }

        .dashboard-card-title {
            align-items: flex-start;
        }

        .dashboard-card-title h2 {
            margin-bottom: 4px;
        }

        .dashboard-card-title p {
            margin: 0;
            color: var(--muted);
            font-size: 10px;
        }

        .dashboard-project-link {
            color: var(--green);
        }

        .dashboard-status {
            display: inline-flex;
            align-items: center;
            min-height: 24px;
            padding: 0 8px;
            border-radius: 999px;
            font-size: 8px;
            font-weight: 850;
            white-space: nowrap;
        }

        .dashboard-status-success {
            background: #e8f6ed;
            color: #237046;
        }

        .dashboard-status-danger {
            background: #fff0ef;
            color: #a83a35;
        }

        .dashboard-status-warning {
            background: #fff6df;
            color: #86611b;
        }

        .dashboard-status-info {
            background: #edf4f8;
            color: #315f79;
        }

        .dashboard-empty-state {
            display: grid;
            justify-items: center;
            gap: 12px;
            padding: 28px;
        }

        .dashboard-side-column {
            display: grid;
            gap: 18px;
        }

        .dashboard-directory-section + .dashboard-directory-section {
            margin-top: 18px;
            padding-top: 18px;
            border-top: 1px solid var(--line);
        }

        .dashboard-directory-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .dashboard-directory-heading strong {
            color: var(--ink);
            font-size: 10px;
        }

        .dashboard-directory-heading a {
            color: var(--green2);
            font-size: 9px;
            font-weight: 750;
        }

        .dashboard-directory-item {
            display: flex;
            align-items: center;
            gap: 11px;
            min-height: 52px;
            padding: 8px 0;
        }

        .dashboard-directory-avatar {
            display: grid !important;
            flex: 0 0 36px;
            width: 36px;
            height: 36px;
            margin: 0 !important;
            border-radius: 10px;
            background: var(--mint);
            color: var(--green) !important;
            font-size: 11px !important;
            font-weight: 850;
            line-height: 1;
            overflow: visible !important;
            place-items: center;
            text-align: center;
            text-overflow: clip !important;
            white-space: nowrap;
        }

        .dashboard-directory-avatar.supplier {
            background: #fff5df;
            color: #86611b;
        }

        .dashboard-directory-item > div {
            min-width: 0;
        }

        /* Scope text truncation to the text column only.
           Do not apply these rules to the avatar span. */
        .dashboard-directory-item > div strong,
        .dashboard-directory-item > div span {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .dashboard-directory-item > div strong {
            color: var(--ink);
            font-size: 10px;
        }

        .dashboard-directory-item > div span {
            margin-top: 2px;
            color: var(--muted);
            font-size: 9px;
        }

        .dashboard-project-summary-card {
            padding: 18px;
            background: linear-gradient(135deg, #f5faf7 0%, #fffaf0 100%);
        }

        .dashboard-project-summary-heading {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 15px;
        }

        .dashboard-summary-kicker {
            display: block;
            margin-bottom: 4px;
            color: var(--green2);
            font-size: 8px;
            font-weight: 850;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .dashboard-project-summary-heading h2 {
            margin: 0;
            color: var(--ink);
            font-size: 14px;
        }

        .dashboard-project-summary-heading p {
            margin: 4px 0 0;
            color: var(--muted);
            font-size: 9px;
            line-height: 1.5;
        }

        .dashboard-project-summary-heading > a {
            flex-shrink: 0;
            color: var(--green2);
            font-size: 9px;
            font-weight: 800;
        }

        .dashboard-project-summary-heading > a:hover {
            text-decoration: underline;
        }

        .dashboard-project-summary-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .dashboard-project-summary-item {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
            padding: 12px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: var(--white);
            color: inherit;
            text-decoration: none;
            transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
        }

        a.dashboard-project-summary-item:hover {
            border-color: #b9d4c6;
            box-shadow: 0 8px 20px rgba(21, 54, 43, .08);
            transform: translateY(-1px);
        }

        .dashboard-summary-icon {
            display: grid;
            flex: 0 0 32px;
            width: 32px;
            height: 32px;
            border-radius: 9px;
            font-size: 12px;
            font-weight: 900;
            place-items: center;
        }

        .summary-active .dashboard-summary-icon {
            background: #e7f6ec;
            color: #237046;
        }

        .summary-inactive .dashboard-summary-icon {
            background: #f0f1f0;
            color: #6d7470;
        }

        .dashboard-project-summary-item > div {
            min-width: 0;
        }

        .dashboard-project-summary-item strong {
            display: block;
            color: var(--ink);
            font-size: 16px;
            line-height: 1;
        }

        .dashboard-project-summary-item small {
            display: block;
            margin-top: 4px;
            color: var(--muted);
            font-size: 9px;
            font-weight: 700;
        }

        .dashboard-project-summary-total {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid var(--line);
        }

        .dashboard-project-summary-total span {
            color: var(--muted);
            font-size: 9px;
            font-weight: 700;
        }

        .dashboard-project-summary-total strong {
            color: var(--ink);
            font-size: 13px;
        }

        @media (max-width: 1100px) {
            .dashboard-stat-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .dashboard-content-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 620px) {
            .dashboard-stat-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-project-summary-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush
