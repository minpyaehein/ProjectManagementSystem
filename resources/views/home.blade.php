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
            @if ($currentUser->hasPermission('projects.view'))
                <a
                    class="card dashboard-stat-card"
                    href="{{ route('projectsPage', ['record' => 'active']) }}"
                >
                    <div class="dashboard-stat-top">
                        <span>Active projects</span>
                        <span class="dashboard-stat-icon" aria-hidden="true">▣</span>
                    </div>

                    <strong class="dashboard-stat-value">
                        {{ number_format($activeProjectCount) }}
                    </strong>

                    <span class="dashboard-stat-note">
                        Currently active project records
                    </span>
                </a>
            @else
                <div class="card dashboard-stat-card dashboard-stat-card-static">
                    <div class="dashboard-stat-top">
                        <span>Active projects</span>
                        <span class="dashboard-stat-icon" aria-hidden="true">▣</span>
                    </div>

                    <strong class="dashboard-stat-value">
                        {{ number_format($activeProjectCount) }}
                    </strong>

                    <span class="dashboard-stat-note">
                        Currently active project records
                    </span>
                </div>
            @endif

            @if ($currentUser->hasPermission('customers.view'))
                <a class="card dashboard-stat-card" href="{{ route('customersPage') }}">
                    <div class="dashboard-stat-top">
                        <span>Customers</span>
                        <span class="dashboard-stat-icon" aria-hidden="true">♧</span>
                    </div>

                    <strong class="dashboard-stat-value">
                        {{ number_format($customerCount) }}
                    </strong>

                    <span class="dashboard-stat-note">Total customer records</span>
                </a>
            @else
                <div class="card dashboard-stat-card dashboard-stat-card-static">
                    <div class="dashboard-stat-top">
                        <span>Customers</span>
                        <span class="dashboard-stat-icon" aria-hidden="true">♧</span>
                    </div>

                    <strong class="dashboard-stat-value">
                        {{ number_format($customerCount) }}
                    </strong>

                    <span class="dashboard-stat-note">Total customer records</span>
                </div>
            @endif

            @if ($currentUser->hasPermission('suppliers.view'))
                <a class="card dashboard-stat-card" href="{{ route('suppliersPage') }}">
                    <div class="dashboard-stat-top">
                        <span>Suppliers</span>
                        <span class="dashboard-stat-icon" aria-hidden="true">◇</span>
                    </div>

                    <strong class="dashboard-stat-value">
                        {{ number_format($supplierCount) }}
                    </strong>

                    <span class="dashboard-stat-note">Registered supplier partners</span>
                </a>
            @else
                <div class="card dashboard-stat-card dashboard-stat-card-static">
                    <div class="dashboard-stat-top">
                        <span>Suppliers</span>
                        <span class="dashboard-stat-icon" aria-hidden="true">◇</span>
                    </div>

                    <strong class="dashboard-stat-value">
                        {{ number_format($supplierCount) }}
                    </strong>

                    <span class="dashboard-stat-note">Registered supplier partners</span>
                </div>
            @endif

            @if ($currentUser->hasPermission('users.view'))
                <a
                    class="card dashboard-stat-card"
                    href="{{ route('usersPage', ['status' => 'Active']) }}"
                >
                    <div class="dashboard-stat-top">
                        <span>Active users</span>
                        <span class="dashboard-stat-icon" aria-hidden="true">♙</span>
                    </div>

                    <strong class="dashboard-stat-value">
                        {{ number_format($activeUserCount) }}
                    </strong>

                    <span class="dashboard-stat-note">
                        Across {{ number_format($roleCount) }}
                        {{ $roleCount === 1 ? 'role' : 'roles' }}
                    </span>
                </a>
            @else
                <div class="card dashboard-stat-card dashboard-stat-card-static">
                    <div class="dashboard-stat-top">
                        <span>Active users</span>
                        <span class="dashboard-stat-icon" aria-hidden="true">♙</span>
                    </div>

                    <strong class="dashboard-stat-value">
                        {{ number_format($activeUserCount) }}
                    </strong>

                    <span class="dashboard-stat-note">
                        Across {{ number_format($roleCount) }}
                        {{ $roleCount === 1 ? 'role' : 'roles' }}
                    </span>
                </div>
            @endif
        </div>

        <div class="dashboard-content-grid">
            <section class="card dashboard-chart-card">
                <div class="card-title dashboard-card-title">
                    <div>
                        <h2>Monthly project report</h2>
                        <p>Projects created during the latest six months.</p>
                    </div>

                    @if ($currentUser->hasPermission('projects.view'))
                        <a href="{{ route('projectsPage') }}">View projects →</a>
                    @endif
                </div>

                <div class="dashboard-chart-summary">
                    <div>
                        <span>Six-month total</span>
                        <strong>{{ number_format($sixMonthProjectTotal) }}</strong>
                    </div>

                    <div>
                        <span>Highest month</span>
                        <strong>{{ number_format($monthlyProjectMaximum) }}</strong>
                    </div>
                </div>

                <div
                    class="dashboard-bar-chart"
                    role="img"
                    aria-label="Bar chart showing projects created each month during the latest six months"
                >
                    <div class="dashboard-chart-grid" aria-hidden="true">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>

                    <div class="dashboard-bars">
                        @foreach ($monthlyProjectReport as $month)
                            @php
                                $height = $month['count'] > 0
                                    ? max(
                                        10,
                                        (int) round(
                                            ($month['count'] / $monthlyProjectMaximum) * 100
                                        )
                                    )
                                    : 3;
                            @endphp

                            <div class="dashboard-bar-column">
                                <div class="dashboard-bar-value">
                                    {{ number_format($month['count']) }}
                                </div>

                                <div
                                    class="dashboard-bar-track"
                                    title="{{ $month['full_label'] }}: {{ $month['count'] }} projects"
                                >
                                    <div
                                        class="dashboard-bar-fill {{ $month['count'] === 0 ? 'is-empty' : '' }}"
                                        style="height: {{ $height }}%"
                                    ></div>
                                </div>

                                <div class="dashboard-bar-label">
                                    <strong>{{ $month['label'] }}</strong>
                                    <span>{{ substr($month['key'], 0, 4) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <p class="dashboard-chart-note">
                    Counts are based on each project's creation date.
                </p>
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
                                    <span>
                                        {{ $customer->contact_person ?: 'No primary contact' }}
                                    </span>
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
                                <span
                                    class="dashboard-directory-avatar supplier"
                                    aria-hidden="true"
                                >
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
            min-width: 0;
            color: inherit;
            text-decoration: none;
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        }

        a.dashboard-stat-card:hover {
            border-color: #bdd5c9;
            box-shadow: 0 14px 34px rgba(21, 54, 43, .11);
            transform: translateY(-2px);
        }

        .dashboard-stat-card-static {
            cursor: default;
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
            flex: 0 0 30px;
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
            display: block;
            color: var(--green2);
            font-size: 9px;
            font-weight: 700;
            line-height: 1.45;
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

        .dashboard-chart-card {
            min-width: 0;
        }

        .dashboard-chart-summary {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
        }

        .dashboard-chart-summary > div {
            min-width: 120px;
            padding: 10px 12px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #fafcfb;
        }

        .dashboard-chart-summary span {
            display: block;
            color: var(--muted);
            font-size: 8px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .dashboard-chart-summary strong {
            display: block;
            margin-top: 4px;
            color: var(--green);
            font-size: 17px;
        }

        .dashboard-bar-chart {
            position: relative;
            min-height: 300px;
            padding: 20px 12px 0;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 13px;
            background: linear-gradient(180deg, #fbfdfc 0%, #f6faf7 100%);
        }

        .dashboard-chart-grid {
            position: absolute;
            z-index: 0;
            inset: 22px 12px 54px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            pointer-events: none;
        }

        .dashboard-chart-grid span {
            display: block;
            width: 100%;
            border-top: 1px dashed #dbe5df;
        }

        .dashboard-bars {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: repeat(6, minmax(48px, 1fr));
            align-items: end;
            gap: clamp(8px, 2vw, 20px);
            min-width: 430px;
            height: 275px;
        }

        .dashboard-bar-column {
            display: grid;
            grid-template-rows: 24px minmax(170px, 1fr) 44px;
            align-items: end;
            min-width: 0;
            height: 100%;
        }

        .dashboard-bar-value {
            padding-bottom: 5px;
            color: var(--green);
            font-size: 10px;
            font-weight: 850;
            text-align: center;
        }

        .dashboard-bar-track {
            position: relative;
            width: min(52px, 74%);
            height: 100%;
            margin: 0 auto;
            overflow: hidden;
            border-radius: 10px 10px 4px 4px;
            background: rgba(36, 88, 73, .07);
        }

        .dashboard-bar-fill {
            position: absolute;
            right: 0;
            bottom: 0;
            left: 0;
            min-height: 5px;
            border-radius: 10px 10px 4px 4px;
            background: linear-gradient(180deg, #3f8c72 0%, var(--green) 100%);
        }

        .dashboard-bar-fill.is-empty {
            background: #cfd9d3;
        }

        .dashboard-bar-label {
            padding-top: 8px;
            text-align: center;
        }

        .dashboard-bar-label strong,
        .dashboard-bar-label span {
            display: block;
        }

        .dashboard-bar-label strong {
            color: var(--ink);
            font-size: 10px;
        }

        .dashboard-bar-label span {
            margin-top: 2px;
            color: var(--muted);
            font-size: 8px;
        }

        .dashboard-chart-note {
            margin: 10px 0 0;
            color: var(--muted);
            font-size: 9px;
            line-height: 1.5;
        }

        .dashboard-side-column {
            display: grid;
            min-width: 0;
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
            gap: 10px;
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
            overflow: visible !important;
            border-radius: 10px;
            background: var(--mint);
            color: var(--green) !important;
            font-size: 11px !important;
            font-weight: 850;
            line-height: 1;
            place-items: center;
            text-align: center;
            text-overflow: clip !important;
            white-space: nowrap;
        }

        .dashboard-directory-avatar.supplier {
            background: #fff5df;
            color: #86611b !important;
        }

        .dashboard-directory-item > div {
            min-width: 0;
        }

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

        .dashboard-project-summary-item strong,
        .dashboard-project-summary-item small {
            display: block;
        }

        .dashboard-project-summary-item strong {
            color: var(--ink);
            font-size: 16px;
            line-height: 1;
        }

        .dashboard-project-summary-item small {
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

        @media (max-width: 720px) {
            .dashboard-page {
                padding-right: 14px;
                padding-left: 14px;
            }

            .dashboard-stat-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
            }

            .dashboard-stat-card {
                padding: 14px;
            }

            .dashboard-bar-chart {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }

        @media (max-width: 520px) {
            .dashboard-stat-grid,
            .dashboard-project-summary-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-chart-summary {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .dashboard-chart-summary > div {
                min-width: 0;
            }
        }
    </style>
@endpush
