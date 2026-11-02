@extends('layouts.master')

@section('title', 'Dashboard | Golden Pride')
@section('page-name', 'Dashboard')

@section('content')
    @php
        $currentUser = auth()->user();
        $currentTime = now('Asia/Yangon');
        $hour = (int) $currentTime->format('H');

        $greeting = match (true) {
            $hour < 12 => 'Good morning',
            $hour < 18 => 'Good afternoon',
            default => 'Good evening',
        };

        $overviewCards = [
            [
                'permission' => 'projects.view',
                'route' => route('projectsPage'),
                'label' => 'Projects',
                'value' => $totalProjectCount,
                'note' => 'Total project records',
                'symbol' => 'P',
            ],
            [
                'permission' => 'customers.view',
                'route' => route('customersPage'),
                'label' => 'Customers',
                'value' => $customerCount,
                'note' => 'Customer accounts',
                'symbol' => 'C',
            ],
            [
                'permission' => 'suppliers.view',
                'route' => route('suppliersPage'),
                'label' => 'Suppliers',
                'value' => $supplierCount,
                'note' => 'Supplier partners',
                'symbol' => 'S',
            ],
            [
                'permission' => 'users.view',
                'route' => route('usersPage', ['status' => 'Active']),
                'label' => 'Active users',
                'value' => $activeUserCount,
                'note' => number_format($roleCount) . ' ' . ($roleCount === 1 ? 'role' : 'roles'),
                'symbol' => 'U',
            ],
        ];

        $performanceCards = [
            ['label' => 'In progress', 'value' => number_format($inProgressProjectCount), 'note' => 'Current pipeline'],
            ['label' => 'Won', 'value' => number_format($wonProjectCount), 'note' => 'Completed or paid'],
            ['label' => 'Lost', 'value' => number_format($lostProjectCount), 'note' => 'Lost or cancelled'],
            ['label' => 'Revenue', 'value' => number_format($totalProjectRevenue, 2), 'note' => 'MMK'],
            ['label' => 'Cost', 'value' => number_format($totalProjectCost, 2), 'note' => 'MMK'],
            [
                'label' => 'Gross profit',
                'value' => number_format($totalGrossProfit, 2),
                'note' => 'MMK',
                'negative' => $totalGrossProfit < 0,
            ],
            [
                'label' => 'GP margin',
                'value' => number_format($totalGrossProfitPercent, 2) . '%',
                'note' => 'Gross profit ratio',
                'negative' => $totalGrossProfitPercent < 0,
            ],
        ];
    @endphp

    <section class="content dashboard-page">
        <header class="dashboard-hero">
            <div class="dashboard-hero-copy">
                <span class="dashboard-kicker">Workspace overview</span>
                <h1>{{ $greeting }}, {{ $currentUser->name }}.</h1>
                <p>Monitor project activity, commercial performance, and recently added records from one place.</p>
            </div>

            <div class="dashboard-date" aria-label="Current date">
                <span>{{ $currentTime->format('l') }}</span>
                <strong>{{ $currentTime->format('d M Y') }}</strong>
            </div>
        </header>

        <div class="dashboard-overview-grid">
            @foreach ($overviewCards as $card)
                @if ($currentUser->hasPermission($card['permission']))
                    <a class="dashboard-overview-card" href="{{ $card['route'] }}">
                    @else
                        <div class="dashboard-overview-card dashboard-card-static">
                @endif

                <div class="dashboard-overview-top">
                    <span>{{ $card['label'] }}</span>
                    <span class="dashboard-overview-symbol" aria-hidden="true">{{ $card['symbol'] }}</span>
                </div>
                <strong class="dashboard-overview-value">{{ number_format($card['value']) }}</strong>
                <small>{{ $card['note'] }}</small>

                @if ($currentUser->hasPermission($card['permission']))
                    </a>
                @else
        </div>
        @endif
        @endforeach
        </div>

        @if ($currentUser->hasPermission('projects.view'))
            <section class="dashboard-panel dashboard-performance-panel">
                <div class="dashboard-section-heading">
                    <div>
                        <span class="dashboard-kicker">Project performance</span>
                        <h2>Commercial and progress overview</h2>
                    </div>
                    <a href="{{ route('projectsPage') }}">View projects →</a>
                </div>

                <div class="dashboard-performance-grid">
                    @foreach ($performanceCards as $card)
                        <div class="dashboard-performance-card">
                            <span>{{ $card['label'] }}</span>
                            <strong
                                class="{{ $card['negative'] ?? false ? 'is-negative' : '' }}">{{ $card['value'] }}</strong>
                            <small>{{ $card['note'] }}</small>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <div class="dashboard-analytics-grid">
            <section class="dashboard-panel dashboard-chart-panel">
                <div class="dashboard-section-heading">
                    <div>
                        <span class="dashboard-kicker">Six-month trend</span>
                        <h2>Monthly project report</h2>
                        <p>Projects grouped by the Project Create Date entered in the form.</p>
                    </div>
                    <div class="dashboard-summary-pills">
                        <div><span>Total</span><strong>{{ number_format($sixMonthProjectTotal) }}</strong></div>
                        <div><span>Peak</span><strong>{{ number_format($highestMonthlyProjectCount) }}</strong></div>
                    </div>
                </div>

                <div class="dashboard-chart" role="img" aria-label="Monthly project totals for six months">
                    <div class="dashboard-chart-lines" aria-hidden="true">
                        <span></span><span></span><span></span><span></span></div>
                    <div class="dashboard-bars">
                        @foreach ($monthlyProjectReport as $month)
                            @php
                                $height =
                                    $month['count'] > 0
                                        ? max(10, (int) round(($month['count'] / $monthlyProjectMaximum) * 100))
                                        : 3;
                            @endphp
                            <div class="dashboard-bar-column"
                                title="{{ $month['full_label'] }}: {{ $month['count'] }} projects">
                                <strong>{{ number_format($month['count']) }}</strong>
                                <div class="dashboard-bar-track">
                                    <div class="dashboard-bar-fill {{ $month['count'] === 0 ? 'is-empty' : '' }}"
                                        style="height: {{ $height }}%"></div>
                                </div>
                                <div class="dashboard-bar-label">
                                    <b>{{ $month['label'] }}</b><span>{{ substr($month['key'], 0, 4) }}</span></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <aside class="dashboard-panel dashboard-status-panel">
                <div class="dashboard-section-heading compact">
                    <div>
                        <span class="dashboard-kicker">Pipeline</span>
                        <h2>Progress status</h2>
                    </div>
                    <strong class="dashboard-count-badge">{{ number_format($totalProjectCount) }}</strong>
                </div>

                @forelse ($projectStatusCounts as $status)
                    @php
                        $statusPercent =
                            $totalProjectCount > 0
                                ? max(4, min(100, ($status['count'] / $totalProjectCount) * 100))
                                : 0;
                    @endphp
                    <a class="dashboard-status-row" href="{{ route('projectsPage', ['status' => $status['name']]) }}">
                        <div><span>{{ $status['name'] }}</span><strong>{{ number_format($status['count']) }}</strong>
                        </div>
                        <div class="dashboard-status-track"><span style="width: {{ $statusPercent }}%"></span></div>
                    </a>
                @empty
                    <p class="dashboard-empty">No project status records found.</p>
                @endforelse
            </aside>
        </div>

        <section class="dashboard-panel dashboard-directory-panel">
            <div class="dashboard-section-heading">
                <div>
                    <span class="dashboard-kicker">Directory activity</span>
                    <h2>Recently added records</h2>
                    <p>The latest customer and supplier records in the workspace.</p>
                </div>
            </div>

            <div class="dashboard-directory-grid">
                <div class="dashboard-directory-group">
                    <div class="dashboard-directory-heading">
                        <strong>Customers</strong>
                        @if ($currentUser->hasPermission('customers.view'))
                            <a href="{{ route('customersPage') }}">View all</a>
                        @endif
                    </div>

                    <div class="dashboard-record-list">
                        @forelse ($recentCustomers as $customer)
                            @php
                                $customerSecondary = null;
                                if (
                                    filled($customer->contact_person) &&
                                    strcasecmp(
                                        trim((string) $customer->contact_person),
                                        trim((string) $customer->name),
                                    ) !== 0
                                ) {
                                    $customerSecondary = 'Contact: ' . $customer->contact_person;
                                } elseif (filled($customer->phone)) {
                                    $customerSecondary = $customer->phone;
                                } elseif (filled($customer->email)) {
                                    $customerSecondary = $customer->email;
                                }
                            @endphp
                            <div class="dashboard-record-item">
                                <span class="dashboard-record-avatar"
                                    aria-hidden="true">{{ strtoupper(mb_substr($customer->name, 0, 1)) }}</span>
                                <div class="dashboard-record-content">
                                    <strong title="{{ $customer->name }}">{{ $customer->name }}</strong>
                                    <span>{{ $customerSecondary ?: 'No contact information' }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="dashboard-empty">No customer records found.</p>
                        @endforelse
                    </div>
                </div>

                <div class="dashboard-directory-group">
                    <div class="dashboard-directory-heading">
                        <strong>Suppliers</strong>
                        @if ($currentUser->hasPermission('suppliers.view'))
                            <a href="{{ route('suppliersPage') }}">View all</a>
                        @endif
                    </div>

                    <div class="dashboard-record-list">
                        @forelse ($recentSuppliers as $supplier)
                            @php
                                $supplierSecondary = filled($supplier->category)
                                    ? $supplier->category
                                    : (filled($supplier->phone)
                                        ? $supplier->phone
                                        : (filled($supplier->email)
                                            ? $supplier->email
                                            : 'No supplier details'));
                            @endphp
                            <div class="dashboard-record-item">
                                <span class="dashboard-record-avatar"
                                    aria-hidden="true">{{ strtoupper(mb_substr($supplier->name, 0, 1)) }}</span>
                                <div class="dashboard-record-content">
                                    <strong title="{{ $supplier->name }}">{{ $supplier->name }}</strong>
                                    <span>{{ $supplierSecondary }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="dashboard-empty">No supplier records found.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>
    </section>
@endsection

@push('styles')
    <style>
        .dashboard-page {
            min-width: 0
        }

        .dashboard-hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 18px;
            padding: 28px;
            border-radius: 18px;
            background: linear-gradient(135deg, #123f35, #247b65);
            color: #fff;
            box-shadow: 0 18px 42px rgba(18, 63, 53, .16)
        }

        .dashboard-hero-copy {
            min-width: 0
        }

        .dashboard-kicker {
            display: block;
            margin-bottom: 7px;
            color: var(--green2);
            font-size: 8px;
            font-weight: 900;
            letter-spacing: .14em;
            text-transform: uppercase
        }

        .dashboard-hero .dashboard-kicker {
            color: #c9e3d7
        }

        .dashboard-hero h1 {
            margin: 0;
            font-size: 30px;
            line-height: 1.2
        }

        .dashboard-hero p {
            margin: 8px 0 0;
            color: #d7e8e2;
            font-size: 11px
        }

        .dashboard-date {
            display: grid;
            flex: 0 0 118px;
            min-height: 68px;
            padding: 12px;
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 12px;
            background: rgba(255, 255, 255, .09);
            text-align: center;
            place-content: center
        }

        .dashboard-date span {
            font-size: 8px
        }

        .dashboard-date strong {
            margin-top: 4px;
            font-size: 11px
        }

        .dashboard-overview-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 18px
        }

        .dashboard-overview-card,
        .dashboard-performance-card {
            border: 1px solid var(--line);
            background: var(--white);
            box-shadow: 0 10px 28px rgba(21, 54, 43, .055)
        }

        .dashboard-overview-card {
            display: block;
            min-width: 0;
            padding: 17px;
            border-radius: 14px;
            color: inherit;
            text-decoration: none;
            transition: .18s
        }

        .dashboard-overview-card::before,
        .dashboard-performance-card::before {
            display: none !important;
            content: none !important
        }

        a.dashboard-overview-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 34px rgba(21, 54, 43, .1)
        }

        .dashboard-overview-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            color: var(--muted);
            font-size: 9px;
            font-weight: 800
        }

        .dashboard-overview-symbol {
            display: grid;
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: #edf4f0;
            color: var(--green);
            font-size: 10px;
            font-weight: 900;
            place-items: center
        }

        .dashboard-overview-value {
            display: block;
            margin: 15px 0 7px;
            color: var(--green);
            font-size: 25px
        }

        .dashboard-overview-card small {
            color: var(--muted);
            font-size: 8px
        }

        .dashboard-panel {
            min-width: 0;
            padding: 19px;
            border: 1px solid var(--line);
            border-radius: 16px;
            background: var(--white);
            box-shadow: 0 12px 34px rgba(21, 54, 43, .055)
        }

        .dashboard-performance-panel {
            margin-bottom: 18px
        }

        .dashboard-section-heading {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 15px
        }

        .dashboard-section-heading.compact {
            align-items: center
        }

        .dashboard-section-heading h2 {
            margin: 0;
            color: var(--ink);
            font-size: 16px
        }

        .dashboard-section-heading p {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 9px
        }

        .dashboard-section-heading>a,
        .dashboard-directory-heading>a {
            color: var(--green2);
            font-size: 9px;
            font-weight: 800;
            text-decoration: none
        }

        .dashboard-performance-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 10px
        }

        .dashboard-performance-card {
            display: flex;
            min-width: 0;
            min-height: 104px;
            padding: 13px;
            border-radius: 11px;
            flex-direction: column
        }

        .dashboard-performance-card>span {
            color: var(--muted);
            font-size: 8px;
            font-weight: 850;
            text-transform: uppercase
        }

        .dashboard-performance-card>strong {
            display: block;
            margin: 14px 0 7px;
            color: var(--green);
            font-size: 18px;
            overflow: hidden;
            text-overflow: ellipsis
        }

        .dashboard-performance-card>strong.is-negative {
            color: #a53d3a
        }

        .dashboard-performance-card>small {
            margin-top: auto;
            color: var(--muted);
            font-size: 7px
        }

        .dashboard-analytics-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.8fr) minmax(280px, .72fr);
            align-items: start;
            gap: 18px;
            margin-bottom: 18px
        }

        .dashboard-summary-pills {
            display: flex;
            gap: 7px
        }

        .dashboard-summary-pills>div {
            min-width: 68px;
            padding: 9px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #f8faf9;
            text-align: center
        }

        .dashboard-summary-pills span,
        .dashboard-summary-pills strong {
            display: block
        }

        .dashboard-summary-pills span {
            color: var(--muted);
            font-size: 7px;
            font-weight: 850;
            text-transform: uppercase
        }

        .dashboard-summary-pills strong {
            margin-top: 4px;
            color: var(--green);
            font-size: 13px
        }

        .dashboard-chart {
            position: relative;
            height: 270px;
            padding: 17px 10px 0;
            overflow-x: auto;
            border: 1px solid var(--line);
            border-radius: 13px;
            background: linear-gradient(180deg, #fbfdfc, #f7faf8)
        }

        .dashboard-chart-lines {
            position: absolute;
            inset: 25px 12px 50px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            pointer-events: none
        }

        .dashboard-chart-lines span {
            border-top: 1px dashed #dce6e0
        }

        .dashboard-bars {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: repeat(6, minmax(55px, 1fr));
            align-items: end;
            gap: 18px;
            min-width: 480px;
            height: 245px
        }

        .dashboard-bar-column {
            display: grid;
            grid-template-rows: 25px minmax(155px, 1fr) 42px;
            align-items: end;
            height: 100%;
            text-align: center
        }

        .dashboard-bar-column>strong {
            padding-bottom: 5px;
            color: var(--green);
            font-size: 9px
        }

        .dashboard-bar-track {
            position: relative;
            width: min(50px, 72%);
            height: 100%;
            margin: auto;
            border-radius: 10px 10px 4px 4px;
            background: #edf2ef;
            overflow: hidden
        }

        .dashboard-bar-fill {
            position: absolute;
            right: 0;
            bottom: 0;
            left: 0;
            min-height: 5px;
            border-radius: 10px 10px 4px 4px;
            background: #4aa184
        }

        .dashboard-bar-fill.is-empty {
            background: #d6dfda
        }

        .dashboard-bar-label b,
        .dashboard-bar-label span {
            display: block
        }

        .dashboard-bar-label b {
            font-size: 9px
        }

        .dashboard-bar-label span {
            margin-top: 2px;
            color: var(--muted);
            font-size: 7px
        }

        .dashboard-count-badge {
            display: grid;
            min-width: 38px;
            height: 34px;
            padding: 0 10px;
            border-radius: 9px;
            background: #eaf3ee;
            color: var(--green);
            font-size: 11px;
            place-items: center
        }

        .dashboard-status-row {
            display: block;
            padding: 9px 0;
            color: inherit;
            text-decoration: none
        }

        .dashboard-status-row>div:first-child {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 6px;
            font-size: 9px
        }

        .dashboard-status-row>div:first-child span {
            font-weight: 750
        }

        .dashboard-status-row>div:first-child strong {
            color: var(--muted)
        }

        .dashboard-status-track {
            height: 5px;
            border-radius: 999px;
            background: #edf2ef;
            overflow: hidden
        }

        .dashboard-status-track span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: #5ca88e
        }

        .dashboard-directory-panel {
            margin-bottom: 18px
        }

        .dashboard-directory-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px
        }

        .dashboard-directory-group {
            min-width: 0;
            padding: 15px;
            border: 1px solid var(--line);
            border-radius: 13px;
            background: #f8faf9
        }

        .dashboard-directory-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 11px
        }

        .dashboard-directory-heading>strong {
            font-size: 10px
        }

        .dashboard-record-list {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 9px
        }

        .dashboard-record-item {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
            padding: 11px;
            border: 1px solid #e1e8e4;
            border-radius: 11px;
            background: #fff
        }

        .dashboard-record-avatar {
            display: grid;
            flex: 0 0 36px;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #e6f2ec;
            color: var(--green);
            font-size: 10px;
            font-weight: 900;
            place-items: center
        }

        .dashboard-record-content {
            flex: 1;
            min-width: 0
        }

        .dashboard-record-content strong,
        .dashboard-record-content span {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap
        }

        .dashboard-record-content strong {
            color: var(--ink);
            font-size: 9px
        }

        .dashboard-record-content span {
            margin-top: 3px;
            color: var(--muted);
            font-size: 8px
        }

        .dashboard-empty {
            margin: 0;
            padding: 14px;
            color: var(--muted);
            font-size: 9px;
            text-align: center
        }

        .dashboard-card-static {
            cursor: default
        }

        @media(max-width:1200px) {
            .dashboard-performance-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr))
            }

            .dashboard-record-list {
                grid-template-columns: repeat(2, minmax(0, 1fr))
            }
        }

        @media(max-width:1000px) {
            .dashboard-overview-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr))
            }

            .dashboard-analytics-grid {
                grid-template-columns: 1fr
            }

            .dashboard-directory-grid {
                grid-template-columns: 1fr
            }
        }

        @media(max-width:700px) {
            .dashboard-page {
                padding-right: 14px;
                padding-left: 14px
            }

            .dashboard-hero {
                align-items: flex-start;
                padding: 21px;
                flex-direction: column
            }

            .dashboard-hero h1 {
                font-size: 23px
            }

            .dashboard-date {
                width: 100%;
                grid-auto-flow: column;
                justify-content: space-between;
                min-height: auto
            }

            .dashboard-performance-grid,
            .dashboard-record-list {
                grid-template-columns: repeat(2, minmax(0, 1fr))
            }

            .dashboard-section-heading {
                flex-direction: column
            }

            .dashboard-summary-pills {
                width: 100%
            }

            .dashboard-summary-pills>div {
                flex: 1
            }
        }

        @media(max-width:480px) {

            .dashboard-overview-grid,
            .dashboard-performance-grid,
            .dashboard-record-list {
                grid-template-columns: 1fr
            }
        }
    </style>
@endpush
