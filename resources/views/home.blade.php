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
                'symbol' => 'PR',
            ],
            [
                'permission' => 'customers.view',
                'route' => route('customersPage'),
                'label' => 'Customers',
                'value' => $customerCount,
                'note' => 'Customer accounts',
                'symbol' => 'CU',
            ],
            [
                'permission' => 'suppliers.view',
                'route' => route('suppliersPage'),
                'label' => 'Suppliers',
                'value' => $supplierCount,
                'note' => 'Supplier partners',
                'symbol' => 'SU',
            ],
            [
                'permission' => 'users.view',
                'route' => route('usersPage', ['status' => 'Active']),
                'label' => 'Active users',
                'value' => $activeUserCount,
                'note' => number_format($roleCount) . ' ' . ($roleCount === 1 ? 'role' : 'roles'),
                'symbol' => 'US',
            ],
        ];

        $performanceCards = [
            [
                'label' => 'In progress',
                'value' => number_format($inProgressProjectCount),
                'note' => 'Current project pipeline',
                'class' => 'metric-progress',
            ],
            [
                'label' => 'Won',
                'value' => number_format($wonProjectCount),
                'note' => 'Completed or paid',
                'class' => 'metric-positive',
            ],
            [
                'label' => 'Lost',
                'value' => number_format($lostProjectCount),
                'note' => 'Lost or cancelled',
                'class' => 'metric-negative',
            ],
            [
                'label' => 'Revenue',
                'value' => number_format($totalProjectRevenue, 2),
                'note' => 'MMK',
                'class' => 'metric-financial',
            ],
            [
                'label' => 'Cost',
                'value' => number_format($totalProjectCost, 2),
                'note' => 'MMK',
                'class' => 'metric-financial',
            ],
            [
                'label' => 'Gross profit',
                'value' => number_format($totalGrossProfit, 2),
                'note' => 'MMK',
                'class' => $totalGrossProfit < 0 ? 'metric-negative' : 'metric-positive',
            ],
            [
                'label' => 'GP margin',
                'value' => number_format($totalGrossProfitPercent, 2) . '%',
                'note' => 'Gross profit ratio',
                'class' => $totalGrossProfitPercent < 0 ? 'metric-negative' : 'metric-positive',
            ],
        ];

        $statusTotal = max(1, (int) collect($projectStatusCounts)->sum('count'));
    @endphp

    <section class="content dashboard-page">
        <header class="dashboard-hero">
            <div class="dashboard-hero-content">
                <div class="dashboard-hero-copy">
                    <span class="dashboard-kicker">Workspace overview</span>
                    <h1>{{ $greeting }}, {{ $currentUser->name }}.</h1>
                    <p>Monitor project activity, commercial performance, and recent records from one workspace.</p>
                </div>

                <div class="dashboard-date-card" aria-label="Current date">
                    <span>{{ $currentTime->format('l') }}</span>
                    <strong>{{ $currentTime->format('d M Y') }}</strong>
                </div>
            </div>

            <div class="dashboard-hero-summary">
                <div>
                    <span>Projects</span>
                    <strong>{{ number_format($totalProjectCount) }}</strong>
                </div>
                <div>
                    <span>Revenue</span>
                    <strong>{{ number_format($totalProjectRevenue, 0) }} MMK</strong>
                </div>
                <div>
                    <span>GP margin</span>
                    <strong>{{ number_format($totalGrossProfitPercent, 2) }}%</strong>
                </div>
            </div>
        </header>

        <div class="dashboard-overview-grid">
            @foreach ($overviewCards as $card)
                @if ($currentUser->hasPermission($card['permission']))
                    <a class="dashboard-overview-card" href="{{ $card['route'] }}">
                    @else
                        <div class="dashboard-overview-card is-static">
                @endif
                <div class="dashboard-overview-head">
                    <span class="dashboard-overview-label">{{ $card['label'] }}</span>
                    <span class="dashboard-overview-symbol" aria-hidden="true">{{ $card['symbol'] }}</span>
                </div>
                <strong class="dashboard-overview-value">{{ number_format($card['value']) }}</strong>
                <span class="dashboard-overview-note">{{ $card['note'] }}</span>
                @if ($currentUser->hasPermission($card['permission']))
                    </a>
                @else
        </div>
        @endif
        @endforeach
        </div>

        @if ($currentUser->hasPermission('projects.view'))
            <section class="dashboard-panel dashboard-performance-panel">
                <div class="dashboard-panel-heading">
                    <div>
                        <span class="dashboard-section-kicker">Project performance</span>
                        <h2>Commercial and progress overview</h2>
                        <p>Combined project outcomes and financial totals.</p>
                    </div>
                    <a href="{{ route('projectsPage') }}">View projects <span aria-hidden="true">→</span></a>
                </div>

                <div class="dashboard-performance-grid">
                    @foreach ($performanceCards as $metric)
                        <article class="dashboard-performance-card {{ $metric['class'] }}">
                            <span class="dashboard-performance-label">{{ $metric['label'] }}</span>
                            <strong class="dashboard-performance-value">{{ $metric['value'] }}</strong>
                            <span class="dashboard-performance-note">{{ $metric['note'] }}</span>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        <div class="dashboard-main-grid">
            <section class="dashboard-panel dashboard-chart-panel">
                <div class="dashboard-panel-heading">
                    <div>
                        <span class="dashboard-section-kicker">Six-month trend</span>
                        <h2>Monthly project report</h2>
                        <p>Projects grouped by the Project Create Date entered in the form.</p>
                    </div>
                    <div class="dashboard-chart-totals">
                        <div><span>Total</span><strong>{{ number_format($sixMonthProjectTotal) }}</strong></div>
                        <div><span>Peak</span><strong>{{ number_format($highestMonthlyProjectCount) }}</strong></div>
                    </div>
                </div>

                <div class="dashboard-chart" role="img" aria-label="Monthly project report for six months">
                    <div class="dashboard-chart-lines" aria-hidden="true">
                        <span></span><span></span><span></span><span></span>
                    </div>

                    <div class="dashboard-chart-bars">
                        @foreach ($monthlyProjectReport as $month)
                            @php
                                $height =
                                    $month['count'] > 0
                                        ? max(12, (int) round(($month['count'] / $monthlyProjectMaximum) * 100))
                                        : 4;
                            @endphp
                            <div class="dashboard-chart-column">
                                <span class="dashboard-chart-value">{{ number_format($month['count']) }}</span>
                                <div class="dashboard-chart-track"
                                    title="{{ $month['full_label'] }}: {{ $month['count'] }} projects">
                                    <div class="dashboard-chart-fill {{ $month['count'] === 0 ? 'is-empty' : '' }}"
                                        style="height: {{ $height }}%"></div>
                                </div>
                                <div class="dashboard-chart-label">
                                    <strong>{{ $month['label'] }}</strong>
                                    <span>{{ substr($month['key'], 0, 4) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <aside class="dashboard-side-stack">
                <section class="dashboard-panel dashboard-status-panel">
                    <div class="dashboard-panel-heading compact">
                        <div>
                            <span class="dashboard-section-kicker">Pipeline</span>
                            <h2>Progress status</h2>
                        </div>
                        <span class="dashboard-total-badge">{{ number_format($totalProjectCount) }}</span>
                    </div>

                    <div class="dashboard-status-list">
                        @forelse ($projectStatusCounts as $status)
                            @php
                                $statusPercent = min(100, max(0, ($status['count'] / $statusTotal) * 100));
                            @endphp
                            @if ($currentUser->hasPermission('projects.view'))
                                <a class="dashboard-status-row"
                                    href="{{ route('projectsPage', ['status' => $status['name']]) }}">
                                @else
                                    <div class="dashboard-status-row">
                            @endif
                            <div class="dashboard-status-head">
                                <span>{{ $status['name'] }}</span>
                                <strong>{{ number_format($status['count']) }}</strong>
                            </div>
                            <div class="dashboard-status-track" aria-hidden="true">
                                <span style="width: {{ $statusPercent }}%"></span>
                            </div>
                            @if ($currentUser->hasPermission('projects.view'))
                                </a>
                            @else
                    </div>
                    @endif
                @empty
                    <p class="dashboard-empty">No project status records found.</p>
                    @endforelse
        </div>
    </section>

    <section class="dashboard-panel dashboard-quick-panel">
        <div class="dashboard-panel-heading compact">
            <div>
                <span class="dashboard-section-kicker">Quick access</span>
                <h2>Common actions</h2>
            </div>
        </div>
        <div class="dashboard-quick-actions">
            @if ($currentUser->hasPermission('projects.create'))
                <a href="{{ route('project-create') }}">Create project <span>→</span></a>
            @endif
            @if ($currentUser->hasPermission('customers.create'))
                <a href="{{ route('customers.create') }}">Add customer <span>→</span></a>
            @endif
            @if ($currentUser->hasPermission('suppliers.create'))
                <a href="{{ route('suppliers.create') }}">Add supplier <span>→</span></a>
            @endif
            @if ($currentUser->hasPermission('users.create'))
                <a href="{{ route('users.create') }}">Create user <span>→</span></a>
            @endif
        </div>
    </section>
    </aside>
    </div>

    <section class="dashboard-panel dashboard-directory-panel">
        <div class="dashboard-panel-heading">
            <div>
                <span class="dashboard-section-kicker">Directory activity</span>
                <h2>Recently added records</h2>
                <p>The latest customer and supplier records in the workspace.</p>
            </div>
        </div>

        <div class="dashboard-directory-grid">
            <div class="dashboard-directory-block">
                <div class="dashboard-directory-title">
                    <strong>Customers</strong>
                    @if ($currentUser->hasPermission('customers.view'))
                        <a href="{{ route('customersPage') }}">View all</a>
                    @endif
                </div>
                <div class="dashboard-directory-list">
                    @forelse ($recentCustomers as $customer)
                        <div class="dashboard-directory-item">
                            <span class="dashboard-avatar">{{ strtoupper(mb_substr($customer->name, 0, 1)) }}</span>
                            <div>
                                <strong>{{ $customer->name }}</strong>
                                <span>{{ $customer->contact_person ?: 'No primary contact' }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="dashboard-empty">No customer records found.</p>
                    @endforelse
                </div>
            </div>

            <div class="dashboard-directory-block">
                <div class="dashboard-directory-title">
                    <strong>Suppliers</strong>
                    @if ($currentUser->hasPermission('suppliers.view'))
                        <a href="{{ route('suppliersPage') }}">View all</a>
                    @endif
                </div>
                <div class="dashboard-directory-list">
                    @forelse ($recentSuppliers as $supplier)
                        <div class="dashboard-directory-item">
                            <span class="dashboard-avatar">{{ strtoupper(mb_substr($supplier->name, 0, 1)) }}</span>
                            <div>
                                <strong>{{ $supplier->name }}</strong>
                                <span>{{ $supplier->category ?: 'No category' }}</span>
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
            --dash-surface: #fff;
            --dash-soft: #f6f9f7;
            --dash-border: #dfe7e2;
            --dash-ink: #163f34;
            --dash-muted: #708078;
            --dash-accent: #226b57;
            --dash-accent-soft: #e9f3ee;
            min-width: 0
        }

        .dashboard-hero {
            position: relative;
            margin-bottom: 18px;
            padding: 28px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 20px;
            background: linear-gradient(125deg, #123f34 0%, #17604e 55%, #2c826b 100%);
            box-shadow: 0 18px 44px rgba(20, 67, 54, .18);
            color: #fff
        }

        .dashboard-hero::after {
            position: absolute;
            right: -80px;
            bottom: -145px;
            width: 330px;
            height: 330px;
            border: 48px solid rgba(255, 255, 255, .055);
            border-radius: 50%;
            content: ""
        }

        .dashboard-hero-content {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px
        }

        .dashboard-kicker,
        .dashboard-section-kicker {
            display: block;
            margin-bottom: 7px;
            font-size: 8px;
            font-weight: 900;
            letter-spacing: .14em;
            text-transform: uppercase
        }

        .dashboard-kicker {
            color: #cde5dc
        }

        .dashboard-hero h1 {
            margin: 0;
            font-size: clamp(25px, 3vw, 39px);
            line-height: 1.12
        }

        .dashboard-hero-copy p {
            max-width: 720px;
            margin: 9px 0 0;
            color: #d7e9e3;
            font-size: 11px;
            line-height: 1.6
        }

        .dashboard-date-card {
            display: grid;
            flex: 0 0 132px;
            padding: 13px;
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 13px;
            background: rgba(255, 255, 255, .09);
            backdrop-filter: blur(8px);
            text-align: right
        }

        .dashboard-date-card span {
            color: #cfe5de;
            font-size: 9px
        }

        .dashboard-date-card strong {
            margin-top: 3px;
            font-size: 12px
        }

        .dashboard-hero-summary {
            position: relative;
            z-index: 1;
            display: none;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
            margin-top: 20px
        }

        .dashboard-hero-summary>div {
            padding: 11px;
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 10px;
            background: rgba(255, 255, 255, .07)
        }

        .dashboard-hero-summary span,
        .dashboard-hero-summary strong {
            display: block
        }

        .dashboard-hero-summary span {
            color: #cfe5de;
            font-size: 8px
        }

        .dashboard-hero-summary strong {
            margin-top: 4px;
            font-size: 12px
        }

        .dashboard-overview-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 18px
        }

        .dashboard-overview-card,
        .dashboard-performance-card {
            position: relative;
            min-width: 0;
            border: 1px solid var(--dash-border);
            background: var(--dash-surface);
            box-shadow: 0 10px 26px rgba(25, 61, 50, .065)
        }

        .dashboard-overview-card {
            display: block;
            padding: 17px;
            border-radius: 15px;
            color: inherit;
            text-decoration: none;
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease
        }

        .dashboard-overview-card::before,
        .dashboard-performance-card::before {
            display: none !important;
            content: none !important
        }

        .dashboard-overview-card:not(.is-static):hover {
            border-color: #bdd1c7;
            transform: translateY(-3px);
            box-shadow: 0 16px 32px rgba(25, 61, 50, .11)
        }

        .dashboard-overview-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px
        }

        .dashboard-overview-label {
            color: var(--dash-muted);
            font-size: 9px;
            font-weight: 850;
            text-transform: uppercase
        }

        .dashboard-overview-symbol {
            display: grid;
            flex: 0 0 32px;
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: var(--dash-accent-soft);
            color: var(--dash-accent);
            font-size: 8px;
            font-weight: 900;
            place-items: center
        }

        .dashboard-overview-value {
            display: block;
            margin: 17px 0 7px;
            color: var(--dash-ink);
            font-size: 28px;
            line-height: 1
        }

        .dashboard-overview-note {
            display: block;
            color: var(--dash-muted);
            font-size: 9px
        }

        .dashboard-panel {
            min-width: 0;
            padding: 20px;
            border: 1px solid var(--dash-border);
            border-radius: 17px;
            background: var(--dash-surface);
            box-shadow: 0 10px 30px rgba(25, 61, 50, .055)
        }

        .dashboard-performance-panel {
            margin-bottom: 18px
        }

        .dashboard-panel-heading {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 17px
        }

        .dashboard-panel-heading.compact {
            margin-bottom: 14px
        }

        .dashboard-section-kicker {
            color: var(--dash-accent)
        }

        .dashboard-panel-heading h2 {
            margin: 0;
            color: var(--dash-ink);
            font-size: 17px
        }

        .dashboard-panel-heading p {
            margin: 5px 0 0;
            color: var(--dash-muted);
            font-size: 9px;
            line-height: 1.5
        }

        .dashboard-panel-heading>a {
            color: var(--dash-accent);
            font-size: 9px;
            font-weight: 850;
            text-decoration: none
        }

        .dashboard-performance-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 10px
        }

        .dashboard-performance-card {
            display: flex;
            flex-direction: column;
            min-height: 106px;
            padding: 14px;
            border-radius: 12px
        }

        .dashboard-performance-label {
            color: var(--dash-muted);
            font-size: 8px;
            font-weight: 900;
            text-transform: uppercase
        }

        .dashboard-performance-value {
            display: block;
            margin: 15px 0 8px;
            overflow: hidden;
            color: var(--dash-ink);
            font-size: 19px;
            line-height: 1.05;
            text-overflow: ellipsis
        }

        .dashboard-performance-note {
            margin-top: auto;
            color: var(--dash-muted);
            font-size: 8px
        }

        .metric-negative .dashboard-performance-value {
            color: #a1423d
        }

        .metric-positive .dashboard-performance-value {
            color: #1e684e
        }

        .dashboard-main-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.7fr) minmax(280px, .8fr);
            align-items: start;
            gap: 18px;
            margin-bottom: 18px
        }

        .dashboard-chart-totals {
            display: flex;
            gap: 8px
        }

        .dashboard-chart-totals>div {
            min-width: 70px;
            padding: 9px 11px;
            border: 1px solid var(--dash-border);
            border-radius: 10px;
            background: var(--dash-soft);
            text-align: center
        }

        .dashboard-chart-totals span,
        .dashboard-chart-totals strong {
            display: block
        }

        .dashboard-chart-totals span {
            color: var(--dash-muted);
            font-size: 7px;
            font-weight: 850;
            text-transform: uppercase
        }

        .dashboard-chart-totals strong {
            margin-top: 3px;
            color: var(--dash-ink);
            font-size: 14px
        }

        .dashboard-chart {
            position: relative;
            min-height: 315px;
            padding: 20px 12px 0;
            overflow: hidden;
            border: 1px solid var(--dash-border);
            border-radius: 14px;
            background: linear-gradient(180deg, #fbfdfc, #f4f8f5)
        }

        .dashboard-chart-lines {
            position: absolute;
            z-index: 0;
            inset: 23px 12px 55px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            pointer-events: none
        }

        .dashboard-chart-lines span {
            border-top: 1px dashed #dce6e0
        }

        .dashboard-chart-bars {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: repeat(6, minmax(52px, 1fr));
            align-items: end;
            gap: clamp(8px, 2vw, 20px);
            min-width: 440px;
            height: 286px
        }

        .dashboard-chart-column {
            display: grid;
            grid-template-rows: 25px minmax(175px, 1fr) 45px;
            align-items: end;
            height: 100%
        }

        .dashboard-chart-value {
            padding-bottom: 5px;
            color: var(--dash-ink);
            font-size: 9px;
            font-weight: 900;
            text-align: center
        }

        .dashboard-chart-track {
            position: relative;
            width: min(52px, 72%);
            height: 100%;
            margin: auto;
            overflow: hidden;
            border-radius: 11px 11px 5px 5px;
            background: #e8eeea
        }

        .dashboard-chart-fill {
            position: absolute;
            right: 0;
            bottom: 0;
            left: 0;
            min-height: 5px;
            border-radius: 11px 11px 5px 5px;
            background: linear-gradient(180deg, #5cae90, #226b57)
        }

        .dashboard-chart-fill.is-empty {
            background: #d5dfda
        }

        .dashboard-chart-label {
            padding-top: 8px;
            text-align: center
        }

        .dashboard-chart-label strong,
        .dashboard-chart-label span {
            display: block
        }

        .dashboard-chart-label strong {
            font-size: 9px
        }

        .dashboard-chart-label span {
            margin-top: 2px;
            color: var(--dash-muted);
            font-size: 8px
        }

        .dashboard-side-stack {
            display: grid;
            gap: 18px
        }

        .dashboard-total-badge {
            display: grid;
            min-width: 38px;
            height: 30px;
            padding: 0 10px;
            border-radius: 9px;
            background: var(--dash-accent-soft);
            color: var(--dash-accent);
            font-size: 11px;
            font-weight: 900;
            place-items: center
        }

        .dashboard-status-list {
            display: grid;
            gap: 13px
        }

        .dashboard-status-row {
            display: block;
            color: inherit;
            text-decoration: none
        }

        .dashboard-status-head {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 6px
        }

        .dashboard-status-head span {
            font-size: 9px;
            font-weight: 750
        }

        .dashboard-status-head strong {
            font-size: 9px
        }

        .dashboard-status-track {
            height: 5px;
            overflow: hidden;
            border-radius: 999px;
            background: #e7ede9
        }

        .dashboard-status-track span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #2d7a62, #6bb497)
        }

        .dashboard-quick-actions {
            display: grid;
            gap: 8px
        }

        .dashboard-quick-actions a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 11px 12px;
            border: 1px solid var(--dash-border);
            border-radius: 10px;
            background: var(--dash-soft);
            color: var(--dash-ink);
            font-size: 9px;
            font-weight: 800;
            text-decoration: none;
            transition: .16s
        }

        .dashboard-quick-actions a:hover {
            border-color: #bbd0c5;
            background: #eef5f1;
            transform: translateX(2px)
        }

        .dashboard-quick-actions a span {
            color: var(--dash-accent)
        }

        .dashboard-directory-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px
        }

        .dashboard-directory-block {
            padding: 15px;
            border: 1px solid var(--dash-border);
            border-radius: 13px;
            background: var(--dash-soft)
        }

        .dashboard-directory-title {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 8px
        }

        .dashboard-directory-title strong {
            font-size: 10px
        }

        .dashboard-directory-title a {
            color: var(--dash-accent);
            font-size: 9px;
            font-weight: 800
        }

        .dashboard-directory-list {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px
        }

        .dashboard-directory-item {
            display: flex;
            align-items: center;
            gap: 9px;
            min-width: 0;
            padding: 10px;
            border: 1px solid #e4ebe7;
            border-radius: 10px;
            background: #fff
        }

        .dashboard-avatar {
            display: grid;
            flex: 0 0 34px;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: var(--dash-accent-soft);
            color: var(--dash-accent);
            font-size: 10px;
            font-weight: 900;
            place-items: center
        }

        .dashboard-directory-item>div {
            min-width: 0
        }

        .dashboard-directory-item strong,
        .dashboard-directory-item span {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap
        }

        .dashboard-directory-item strong {
            font-size: 9px
        }

        .dashboard-directory-item>div span {
            margin-top: 3px;
            color: var(--dash-muted);
            font-size: 8px
        }

        .dashboard-empty {
            margin: 0;
            color: var(--dash-muted);
            font-size: 9px
        }

        @media(max-width:1200px) {
            .dashboard-performance-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr))
            }

            .dashboard-directory-list {
                grid-template-columns: 1fr
            }
        }

        @media(max-width:1050px) {
            .dashboard-overview-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr))
            }

            .dashboard-main-grid {
                grid-template-columns: 1fr
            }

            .dashboard-directory-list {
                grid-template-columns: repeat(3, minmax(0, 1fr))
            }
        }

        @media(max-width:760px) {
            .dashboard-page {
                padding-right: 14px;
                padding-left: 14px
            }

            .dashboard-hero {
                padding: 21px
            }

            .dashboard-hero-content {
                align-items: flex-start;
                flex-direction: column
            }

            .dashboard-date-card {
                width: 100%;
                text-align: left
            }

            .dashboard-hero-summary {
                display: grid
            }

            .dashboard-performance-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr))
            }

            .dashboard-directory-grid {
                grid-template-columns: 1fr
            }

            .dashboard-directory-list {
                grid-template-columns: 1fr
            }

            .dashboard-chart-panel {
                overflow: hidden
            }

            .dashboard-chart {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch
            }
        }

        @media(max-width:500px) {

            .dashboard-overview-grid,
            .dashboard-performance-grid {
                grid-template-columns: 1fr
            }

            .dashboard-hero-summary {
                grid-template-columns: 1fr
            }

            .dashboard-panel-heading {
                align-items: stretch;
                flex-direction: column
            }

            .dashboard-chart-totals {
                width: 100%
            }

            .dashboard-chart-totals>div {
                flex: 1
            }
        }
    </style>
@endpush
