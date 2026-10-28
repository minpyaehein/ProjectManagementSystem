@extends('layouts.master')

@section('title', 'Projects | Golden Pride')
@section('page-name', 'Projects')

@section('content')
    @php
        $statuses = [
            'Prospecting',
            'Lead',
            'Quotation',
            'Proposal',
            'Negotiation',
            'PO Received',
            'Delivery',
            'Invoice',
            'Payment',
            'Completed',
            'Cancelled',
            'Lost',
        ];

        $projectTypes = ['New Project', 'Renewal'];
    @endphp

    <section class="content project-list-page">
        <div class="project-list-heading">
            <div>
                <h1>Projects</h1>
                <p>Filter projects, review profitability, and open project actions.</p>
            </div>

            @if (auth()->user()->hasPermission('projects.create'))
                <a class="project-new-button" href="{{ route('project-create') }}">
                    <span aria-hidden="true">+</span>
                    New Project
                </a>
            @endif
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="project-list-card">
            <form
                id="projectFilterForm"
                class="project-filter-form"
                method="GET"
                action="{{ route('projectsPage') }}"
            >
                <div class="project-filter-grid">
                    <div class="project-filter-field">
                        <label for="from_date">From Date</label>
                        <input
                            id="from_date"
                            name="from_date"
                            type="date"
                            value="{{ request('from_date') }}"
                        >
                    </div>

                    <div class="project-filter-field">
                        <label for="to_date">To Date</label>
                        <input
                            id="to_date"
                            name="to_date"
                            type="date"
                            value="{{ request('to_date') }}"
                        >
                    </div>

                    <div class="project-filter-field">
                        <label for="search">Project</label>
                        <input
                            id="search"
                            name="search"
                            type="search"
                            value="{{ request('search') }}"
                            placeholder="Project name..."
                        >
                    </div>

                    <div class="project-filter-field">
                        <label for="customer_id">End User</label>
                        <select id="customer_id" name="customer_id">
                            <option value="">All End Users</option>
                            @foreach ($customers as $customer)
                                <option
                                    value="{{ $customer->customer_id }}"
                                    @selected((string) request('customer_id') === (string) $customer->customer_id)
                                >
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="project-filter-field">
                        <label for="supplier_id">Supplier</label>
                        <select id="supplier_id" name="supplier_id">
                            <option value="">All Suppliers</option>
                            @foreach ($suppliers as $supplier)
                                <option
                                    value="{{ $supplier->supplier_id }}"
                                    @selected((string) request('supplier_id') === (string) $supplier->supplier_id)
                                >
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="project-filter-field">
                        <label for="account_manager">Account Manager</label>
                        <input
                            id="account_manager"
                            name="account_manager"
                            type="search"
                            value="{{ request('account_manager') }}"
                            placeholder="AM name..."
                        >
                    </div>

                    <div class="project-filter-field">
                        <label for="type">Type</label>
                        <select id="type" name="type">
                            <option value="">All Types</option>
                            @foreach ($projectTypes as $type)
                                <option value="{{ $type }}" @selected(request('type') === $type)>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="project-filter-field">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="">All Statuses</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" @selected(request('status') === $status)>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="project-filter-actions">
                        <button class="project-filter-button" type="submit">Apply Filters</button>
                        <a class="project-clear-button" href="{{ route('projectsPage') }}">Clear</a>
                    </div>
                </div>

                <p class="project-filter-help">
                    Date range filters by Start Date. Print/PDF and CSV use the current filtered project list.
                </p>
            </form>

            <div class="project-list-toolbar">
                <div>
                    <h2>Project List</h2>
                    <p>
                        Showing {{ $projects->firstItem() ?? 0 }}–{{ $projects->lastItem() ?? 0 }}
                        of {{ $projects->total() }} projects
                    </p>
                </div>

                <div class="project-list-export-actions">
                    <button
                        class="project-print-button"
                        type="button"
                        onclick="window.print()"
                    >
                        Print / PDF
                    </button>

                    @if (auth()->user()->hasPermission('projects.view'))
                        <a
                            class="project-export-button"
                            href="{{ route('projects.export', request()->query()) }}"
                        >
                            Export CSV
                        </a>
                    @endif
                </div>
            </div>

            <div class="project-table-wrap">
                <table class="project-data-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Project</th>
                            <th>Type</th>
                            <th>AM</th>
                            <th>End User</th>
                            <th>Supplier</th>
                            <th>Vendor</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th class="number-column">GP</th>
                            <th class="number-column">GP %</th>
                            <th class="action-column">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($projects as $project)
                            @php
                                $grossProfit = (float) $project->revenue - (float) $project->cost;
                                $grossProfitPercent = (float) $project->revenue > 0
                                    ? ($grossProfit / (float) $project->revenue) * 100
                                    : 0;

                                $normalizedStatus = strtolower(trim((string) $project->status));
                                $recordLabel = in_array($normalizedStatus, ['completed', 'cancelled', 'lost'], true)
                                    ? 'Closed'
                                    : 'Active';

                                $statusClass = match ($normalizedStatus) {
                                    'completed' => 'project-badge-success',
                                    'cancelled', 'lost' => 'project-badge-danger',
                                    'proposal', 'negotiation' => 'project-badge-warning',
                                    default => 'project-badge-info',
                                };
                            @endphp

                            <tr>
                                <td class="project-item-number">
                                    {{ $projects->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    <a class="project-table-name" href="{{ route('projects.show', $project) }}">
                                        {{ $project->name }}
                                    </a>
                                    <div class="project-table-subtext">
                                        PRJ-{{ str_pad((string) $project->project_id, 4, '0', STR_PAD_LEFT) }}
                                    </div>
                                </td>

                                <td>{{ $project->type ?: '—' }}</td>
                                <td>{{ $project->accountManagerName ?: '—' }}</td>
                                <td>{{ $project->customer?->name ?? '—' }}</td>
                                <td>{{ $project->supplier?->name ?? '—' }}</td>
                                <td>{{ $project->vendor ?: '—' }}</td>
                                <td>{{ $project->productCategory ?: '—' }}</td>

                                <td>
                                    <span class="project-status-badge {{ $statusClass }}">
                                        {{ $project->status }}
                                    </span>
                                </td>

                                <td class="number-column {{ $grossProfit < 0 ? 'negative-number' : '' }}">
                                    {{ number_format($grossProfit, 2) }} MMK
                                </td>

                                <td class="number-column {{ $grossProfitPercent < 0 ? 'negative-number' : '' }}">
                                    {{ number_format($grossProfitPercent, 1) }}%
                                </td>

                                <td>
                                    <div class="project-row-actions">
                                        @if (auth()->user()->hasPermission('projects.view'))
                                            <a href="{{ route('projects.show', $project) }}">View</a>
                                        @endif

                                        @if (auth()->user()->hasPermission('projects.edit'))
                                            <a href="{{ route('projects.edit', $project) }}">Edit</a>
                                        @endif


                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13">
                                    <div class="project-table-empty">
                                        <strong>No projects found.</strong>
                                        <span>Change the filter values or create a new project.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($projects->hasPages())
                <div class="project-list-pagination">
                    {{ $projects->links('vendor.pagination.custom') }}
                </div>
            @endif
        </div>
    </section>
@endsection
