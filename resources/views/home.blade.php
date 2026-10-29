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
            @php
                $statCards = [
                    [
                        'permission' => 'projects.view',
                        'route' => route('projectsPage', ['record' => 'active']),
                        'title' => 'Active projects',
                        'icon' => '▣',
                        'value' => $activeProjectCount,
                        'note' => 'Currently active project records',
                    ],
                    [
                        'permission' => 'customers.view',
                        'route' => route('customersPage'),
                        'title' => 'Customers',
                        'icon' => '♧',
                        'value' => $customerCount,
                        'note' => 'Total customer records',
                    ],
                    [
                        'permission' => 'suppliers.view',
                        'route' => route('suppliersPage'),
                        'title' => 'Suppliers',
                        'icon' => '◇',
                        'value' => $supplierCount,
                        'note' => 'Registered supplier partners',
                    ],
                    [
                        'permission' => 'users.view',
                        'route' => route('usersPage', ['status' => 'Active']),
                        'title' => 'Active users',
                        'icon' => '♙',
                        'value' => $activeUserCount,
                        'note' => 'Across ' . number_format($roleCount) . ' ' . ($roleCount === 1 ? 'role' : 'roles'),
                    ],
                ];
            @endphp

            @foreach ($statCards as $card)
                @if ($currentUser->hasPermission($card['permission']))
                    <a class="card dashboard-stat-card" href="{{ $card['route'] }}">
                @else
                    <div class="card dashboard-stat-card dashboard-stat-card-static">
                @endif

                    <div class="dashboard-stat-top">
                        <span>{{ $card['title'] }}</span>
                        <span class="dashboard-stat-icon" aria-hidden="true">{{ $card['icon'] }}</span>
                    </div>
                    <strong class="dashboard-stat-value">{{ number_format($card['value']) }}</strong>
                    <span class="dashboard-stat-note">{{ $card['note'] }}</span>

                @if ($currentUser->hasPermission($card['permission']))
                    </a>
                @else
                    </div>
                @endif
            @endforeach
        </div>

        <div class="dashboard-content-grid">
            <section class="card dashboard-chart-card">
                <div class="card-title dashboard-card-title">
                    <div>
                        <h2>Monthly project report</h2>
                        <p>Projects grouped by the Project Create Date selected in the project form.</p>
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
                        <strong>{{ number_format($highestMonthlyProjectCount) }}</strong>
                    </div>
                </div>

                <div class="dashboard-bar-chart" role="img" aria-label="Monthly project report for six months">
                    <div class="dashboard-chart-grid" aria-hidden="true">
                        <span></span><span></span><span></span><span></span>
                    </div>

                    <div class="dashboard-bars">
                        @foreach ($monthlyProjectReport as $month)
                            @php
                                $height = $month['count'] > 0
                                    ? max(10, (int) round(($month['count'] / $monthlyProjectMaximum) * 100))
                                    : 3;
                            @endphp

                            <div class="dashboard-bar-column">
                                <div class="dashboard-bar-value">{{ number_format($month['count']) }}</div>
                                <div class="dashboard-bar-track" title="{{ $month['full_label'] }}: {{ $month['count'] }} projects">
                                    <div class="dashboard-bar-fill {{ $month['count'] === 0 ? 'is-empty' : '' }}" style="height: {{ $height }}%"></div>
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
                    Counts are based on the Project Create Date entered by the user, not the automatic database timestamp.
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
                            <span class="dashboard-summary-kicker">Project report</span>
                            <h2>Progress status summary</h2>
                            <p>Projects grouped by the status selected in the project form.</p>
                        </div>
                        @if ($currentUser->hasPermission('projects.view'))
                            <a href="{{ route('projectsPage') }}">View all</a>
                        @endif
                    </div>

                    @if ($projectStatusCounts->isNotEmpty())
                        <div class="dashboard-project-status-grid">
                            @foreach ($projectStatusCounts as $status)
                                @if ($currentUser->hasPermission('projects.view'))
                                    <a
                                        class="dashboard-project-status-item {{ $status['class'] }}"
                                        href="{{ route('projectsPage', ['status' => $status['name']]) }}"
                                        title="View {{ $status['name'] }} projects"
                                    >
                                @else
                                    <div class="dashboard-project-status-item {{ $status['class'] }}">
                                @endif

                                    <span class="dashboard-project-status-icon" aria-hidden="true">
                                        {{ $status['icon'] }}
                                    </span>
                                    <div>
                                        <strong>{{ number_format($status['count']) }}</strong>
                                        <small>{{ $status['name'] }}</small>
                                    </div>

                                @if ($currentUser->hasPermission('projects.view'))
                                    </a>
                                @else
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="dashboard-project-status-empty">No project status records found.</div>
                    @endif

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
.dashboard-stat-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:18px}.dashboard-stat-card{display:block;min-width:0;color:inherit;text-decoration:none;transition:.18s}.dashboard-stat-card-static{cursor:default}a.dashboard-stat-card:hover{transform:translateY(-2px);box-shadow:0 14px 34px rgba(21,54,43,.11)}.dashboard-stat-top{display:flex;align-items:center;justify-content:space-between;gap:12px;color:var(--muted);font-size:10px;font-weight:750}.dashboard-stat-icon{display:grid;flex:0 0 30px;width:30px;height:30px;border-radius:9px;background:var(--mint);color:var(--green);place-items:center}.dashboard-stat-value{display:block;margin:13px 0 6px;font-size:26px;line-height:1}.dashboard-stat-note{display:block;color:var(--green2);font-size:9px;font-weight:700}.dashboard-content-grid{display:grid;grid-template-columns:minmax(0,1.65fr) minmax(280px,.75fr);align-items:start;gap:18px}.dashboard-card-title{align-items:flex-start}.dashboard-card-title h2{margin-bottom:4px}.dashboard-card-title p{margin:0;color:var(--muted);font-size:10px}.dashboard-chart-card,.dashboard-side-column{min-width:0}.dashboard-chart-summary{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:18px}.dashboard-chart-summary>div{min-width:120px;padding:10px 12px;border:1px solid var(--line);border-radius:10px;background:#fafcfb}.dashboard-chart-summary span{display:block;color:var(--muted);font-size:8px;font-weight:800;text-transform:uppercase}.dashboard-chart-summary strong{display:block;margin-top:4px;color:var(--green);font-size:17px}.dashboard-bar-chart{position:relative;min-height:300px;padding:20px 12px 0;overflow:hidden;border:1px solid var(--line);border-radius:13px;background:linear-gradient(180deg,#fbfdfc,#f6faf7)}.dashboard-chart-grid{position:absolute;z-index:0;inset:22px 12px 54px;display:flex;flex-direction:column;justify-content:space-between;pointer-events:none}.dashboard-chart-grid span{border-top:1px dashed #dbe5df}.dashboard-bars{position:relative;z-index:1;display:grid;grid-template-columns:repeat(6,minmax(48px,1fr));align-items:end;gap:clamp(8px,2vw,20px);min-width:430px;height:275px}.dashboard-bar-column{display:grid;grid-template-rows:24px minmax(170px,1fr) 44px;align-items:end;height:100%}.dashboard-bar-value{padding-bottom:5px;color:var(--green);font-size:10px;font-weight:850;text-align:center}.dashboard-bar-track{position:relative;width:min(52px,74%);height:100%;margin:auto;overflow:hidden;border-radius:10px 10px 4px 4px;background:rgba(36,88,73,.07)}.dashboard-bar-fill{position:absolute;right:0;bottom:0;left:0;min-height:5px;border-radius:10px 10px 4px 4px;background:linear-gradient(180deg,#3f8c72,var(--green))}.dashboard-bar-fill.is-empty{background:#cfd9d3}.dashboard-bar-label{padding-top:8px;text-align:center}.dashboard-bar-label strong,.dashboard-bar-label span{display:block}.dashboard-bar-label strong{font-size:10px}.dashboard-bar-label span{margin-top:2px;color:var(--muted);font-size:8px}.dashboard-chart-note{margin:10px 0 0;color:var(--muted);font-size:9px}.dashboard-side-column{display:grid;gap:18px}.dashboard-directory-section+.dashboard-directory-section{margin-top:18px;padding-top:18px;border-top:1px solid var(--line)}.dashboard-directory-heading{display:flex;justify-content:space-between;gap:10px;margin-bottom:10px}.dashboard-directory-heading strong{font-size:10px}.dashboard-directory-heading a{color:var(--green2);font-size:9px;font-weight:750}.dashboard-directory-item{display:flex;align-items:center;gap:11px;min-height:52px;padding:8px 0}.dashboard-directory-avatar{display:grid!important;flex:0 0 36px;width:36px;height:36px;border-radius:10px;background:var(--mint);color:var(--green)!important;font-size:11px!important;font-weight:850;place-items:center}.dashboard-directory-avatar.supplier{background:#fff5df;color:#86611b!important}.dashboard-directory-item>div{min-width:0}.dashboard-directory-item>div strong,.dashboard-directory-item>div span{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.dashboard-directory-item>div strong{font-size:10px}.dashboard-directory-item>div span{margin-top:2px;color:var(--muted);font-size:9px}.dashboard-project-summary-card{padding:18px;background:linear-gradient(135deg,#f5faf7,#fffaf0)}.dashboard-project-summary-heading{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:15px}.dashboard-summary-kicker{display:block;margin-bottom:4px;color:var(--green2);font-size:8px;font-weight:850;text-transform:uppercase}.dashboard-project-summary-heading h2{margin:0;font-size:14px}.dashboard-project-summary-heading p{margin:4px 0 0;color:var(--muted);font-size:9px}.dashboard-project-summary-heading>a{flex-shrink:0;color:var(--green2);font-size:9px;font-weight:800}.dashboard-project-status-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}.dashboard-project-status-item{display:flex;align-items:center;gap:10px;min-width:0;padding:12px;border:1px solid var(--line);border-radius:10px;background:var(--white);color:inherit;text-decoration:none;transition:.18s}a.dashboard-project-status-item:hover{transform:translateY(-1px);box-shadow:0 8px 20px rgba(21,54,43,.08)}.dashboard-project-status-icon{display:grid;flex:0 0 32px;width:32px;height:32px;border-radius:9px;font-size:11px;font-weight:900;place-items:center}.status-success .dashboard-project-status-icon{background:#e7f6ec;color:#237046}.status-warning .dashboard-project-status-icon{background:#fff3d8;color:#8a6415}.status-info .dashboard-project-status-icon{background:#e9f3fb;color:#276684}.status-danger .dashboard-project-status-icon{background:#fde9e7;color:#a53d3a}.status-neutral .dashboard-project-status-icon{background:#eef1f0;color:#5d6763}.dashboard-project-status-item>div{min-width:0}.dashboard-project-status-item strong,.dashboard-project-status-item small{display:block}.dashboard-project-status-item strong{font-size:16px;line-height:1}.dashboard-project-status-item small{margin-top:4px;overflow:hidden;color:var(--muted);font-size:9px;font-weight:750;text-overflow:ellipsis;white-space:nowrap}.dashboard-project-status-empty{padding:24px 14px;border:1px dashed var(--line);border-radius:10px;color:var(--muted);font-size:10px;text-align:center}.dashboard-project-summary-total{display:flex;justify-content:space-between;gap:12px;margin-top:12px;padding-top:12px;border-top:1px solid var(--line)}.dashboard-project-summary-total span{color:var(--muted);font-size:9px;font-weight:700}.dashboard-project-summary-total strong{font-size:13px}@media(max-width:1100px){.dashboard-stat-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.dashboard-content-grid{grid-template-columns:1fr}}@media(max-width:720px){.dashboard-page{padding-right:14px;padding-left:14px}.dashboard-stat-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}.dashboard-stat-card{padding:14px}.dashboard-bar-chart{overflow-x:auto;-webkit-overflow-scrolling:touch}}@media(max-width:520px){.dashboard-stat-grid,.dashboard-project-status-grid{grid-template-columns:1fr}.dashboard-chart-summary{display:grid;grid-template-columns:repeat(2,minmax(0,1fr))}}
</style>
@endpush
