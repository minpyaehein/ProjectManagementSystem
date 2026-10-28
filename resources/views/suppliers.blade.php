@extends('layouts.master')

@section('title', 'Suppliers | Golden Pride')
@section('page-name', 'Suppliers')

@section('content')
    <section class="content suppliers-page">
        <div class="pagehead suppliers-pagehead">
            <div>
                <div class="eyebrow">Directory</div>
                <h1>Suppliers</h1>
                <p class="sub">Manage supplier records and contact persons.</p>
            </div>

            <div class="actions">
                @if (auth()->user()->hasPermission('suppliers.create'))
                    <a class="btn btn-primary" href="{{ route('suppliers.create') }}">
                        ＋ Create supplier
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

        <div class="card suppliers-card">
            <form class="toolbar supplier-filter-form" id="supplierFilterForm" action="{{ route('suppliersPage') }}"
                method="GET">
                <div class="supplier-search-group">
                    <label class="sr-only" for="supplierSearch">
                        Search suppliers
                    </label>

                    <input class="search" id="supplierSearch" name="search" type="search" value="{{ request('search') }}"
                        placeholder="Search suppliers or contacts..." autocomplete="off"
                        aria-label="Search suppliers or contacts">

                    <button class="btn supplier-search-button btn-primary" type="submit">
                        Search
                    </button>

                    @if (request()->filled('search') || request()->filled('status'))
                        <a class="btn supplier-clear-button" href="{{ route('suppliersPage') }}">
                            Clear
                        </a>
                    @endif
                </div>

                <div class="supplier-status-filter">
                    <label class="sr-only" for="supplierStatus">
                        Filter by status
                    </label>

                    <select class="filter" id="supplierStatus" name="status" aria-label="Filter suppliers by status">
                        <option value="">All records</option>
                        <option value="Active" @selected(request('status') === 'Active')>
                            Active
                        </option>
                        <option value="Inactive" @selected(request('status') === 'Inactive')>
                            Inactive
                        </option>
                    </select>
                </div>
            </form>

            <div class="supplier-filter-progress" id="supplierFilterProgress" role="status" hidden>
                Updating supplier results...
            </div>

            <div id="supplierResults" aria-live="polite">
                <div class="table-summary">
                    Total suppliers:
                    <strong>{{ number_format($suppliers->total()) }}</strong>
                </div>

                <div class="table-wrap suppliers-table-wrap">
                    <table class="suppliers-table">
                        <thead>
                            <tr>
                                <th>Supplier</th>
                                <th>Primary contact</th>
                                <th>Category</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Contacts</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($suppliers as $supplier)
                                @php($primary = $supplier->primaryContact)

                                <tr>
                                    <td>
                                        <span class="item-name">{{ $supplier->name }}</span>
                                        <div class="small-muted">
                                            SUP-{{ str_pad((string) $supplier->supplier_id, 4, '0', STR_PAD_LEFT) }}
                                        </div>
                                    </td>

                                    <td>
                                        {{ $primary?->name ?? ($supplier->contact_person ?? 'Not provided') }}
                                    </td>

                                    <td>{{ $supplier->category ?: 'Not provided' }}</td>

                                    <td>
                                        @php($supplierEmail = $primary?->email ?? $supplier->email)

                                        @if ($supplierEmail)
                                            <a class="supplier-email" href="mailto:{{ $supplierEmail }}">
                                                {{ $supplierEmail }}
                                            </a>
                                        @else
                                            Not provided
                                        @endif
                                    </td>

                                    <td>
                                        @php($supplierPhone = $primary?->phone ?? $supplier->phone)

                                        @if ($supplierPhone)
                                            <a class="supplier-phone"
                                                href="tel:{{ preg_replace('/[^0-9+]/', '', $supplierPhone) }}">
                                                {{ $supplierPhone }}
                                            </a>
                                        @else
                                            Not provided
                                        @endif
                                    </td>

                                    <td>{{ number_format($supplier->contacts_count ?? 0) }}</td>

                                    <td>
                                        <span class="pill {{ $supplier->status === 'Inactive' ? 'gray' : 'green' }}">
                                            {{ $supplier->status }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="table-actions">
                                            @if (auth()->user()->hasPermission('suppliers.view'))
                                                <a class="action-btn action-view"
                                                    href="{{ route('suppliers.show', $supplier) }}">
                                                    View
                                                </a>
                                            @endif

                                            @if (auth()->user()->hasPermission('suppliers.edit'))
                                                <a class="action-btn action-edit"
                                                    href="{{ route('suppliers.edit', $supplier) }}">
                                                    Edit
                                                </a>
                                            @endif

                                            @if (auth()->user()->hasPermission('suppliers.deactivate') && $supplier->status !== 'Inactive')
                                                <form action="{{ route('suppliers.deactivate', $supplier) }}"
                                                    method="POST" onsubmit="return confirm('Deactivate this supplier?');">
                                                    @csrf
                                                    @method('PATCH')

                                                    <button class="action-btn action-delete" type="submit">
                                                        Deactivate
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">
                                        <div class="empty-state">
                                            <strong>No suppliers found.</strong>

                                            @if (request()->filled('search') || request()->filled('status'))
                                                <p>Try changing or clearing the current filters.</p>

                                                <a class="btn" href="{{ route('suppliersPage') }}">
                                                    Clear filters
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($suppliers->hasPages())
                    <div class="pagination-wrapper">
                        {{ $suppliers->links('vendor.pagination.custom') }}
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .suppliers-page,
        .suppliers-card {
            min-width: 0;
        }

        .supplier-filter-form {
            align-items: center;
        }

        .supplier-search-group {
            display: flex;
            align-items: center;
            flex: 1 1 620px;
            gap: 6px;
            min-width: 0;
        }

        .supplier-search-group .search {
            flex: 1 1 320px;
            width: 100%;
            min-width: 180px;
            max-width: 280px;
        }

        .supplier-status-filter {
            flex: 0 0 auto;
        }

        .supplier-status-filter .filter {
            min-width: 145px;
        }

        .supplier-filter-progress {
            margin: -4px 0 12px;
            color: var(--green2);
            font-size: 10px;
            font-weight: 750;
        }

        .supplier-filter-progress[hidden] {
            display: none;
        }

        #supplierResults {
            position: relative;
            transition: opacity .15s ease;
        }

        #supplierResults.is-filtering {
            opacity: .55;
            pointer-events: none;
        }

        .suppliers-table-wrap {
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .suppliers-table {
            min-width: 1000px;
        }

        .suppliers-table th:last-child,
        .suppliers-table td:last-child {
            text-align: right;
        }

        .supplier-email,
        .supplier-phone {
            color: var(--green2);
            font-weight: 650;
        }

        .supplier-email:hover,
        .supplier-phone:hover {
            color: var(--green);
            text-decoration: underline;
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
            .suppliers-page {
                padding-right: 14px;
                padding-left: 14px;
            }

            .suppliers-pagehead {
                align-items: stretch;
                flex-direction: column;
            }

            .suppliers-pagehead .actions,
            .suppliers-pagehead .btn {
                width: 100%;
            }

            .suppliers-card {
                padding: 15px;
            }

            .supplier-filter-form {
                align-items: stretch;
                flex-direction: column;
            }

            .supplier-search-group {
                display: grid;
                grid-template-columns: minmax(0, 1fr) auto;
                width: 100%;
                flex-basis: auto;
            }

            .supplier-search-group .search {
                width: 100%;
                min-width: 0;
                max-width: none;
            }

            .supplier-clear-button {
                grid-column: 1 / -1;
                width: 100%;
            }

            .supplier-status-filter,
            .supplier-status-filter .filter {
                width: 100%;
            }

            .table-summary {
                justify-content: flex-start;
            }
        }

        @media (max-width: 420px) {
            .supplier-search-group {
                grid-template-columns: 1fr;
            }

            .supplier-search-button,
            .supplier-clear-button {
                grid-column: auto;
                width: 100%;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('js/supplier-auto-filter.js') }}" defer></script>
@endpush
