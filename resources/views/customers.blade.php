@extends('layouts.master')

@section('title', 'Customers | Golden Pride')
@section('page-name', 'Customers')

@section('content')
    <section class="content customers-page">
        <div class="pagehead customers-pagehead">
            <div>
                <div class="eyebrow">Directory</div>
                <h1>Customers</h1>
                <p class="sub">Manage customer records and contact persons.</p>
            </div>

            <div class="actions">
                @if (auth()->user()->hasPermission('customers.create'))
                    <a class="btn btn-primary" href="{{ route('customers.create') }}">
                        ＋ Create customer
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

        <div class="card customers-card">
            <form class="toolbar customer-filter-form" id="customerFilterForm" action="{{ route('customersPage') }}"
                method="GET">
                <div class="customer-search-group">
                    <label class="sr-only" for="customerSearch">
                        Search customers
                    </label>

                    <input class="search" id="customerSearch" name="search" type="search" value="{{ request('search') }}"
                        placeholder="Search customers or contacts..." autocomplete="off"
                        aria-label="Search customers or contacts">

                    <button class="btn customer-search-button btn-primary" type="submit">
                        Search
                    </button>

                    @if (request()->filled('search') || request()->filled('status'))
                        <a class="btn customer-clear-button" href="{{ route('customersPage') }}">
                            Clear
                        </a>
                    @endif
                </div>

                <div class="customer-status-filter">
                    <label class="sr-only" for="customerStatus">
                        Filter by status
                    </label>

                    <select class="filter" id="customerStatus" name="status" aria-label="Filter customers by status">
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

            <div class="customer-filter-progress" id="customerFilterProgress" hidden>
                Updating customer results...
            </div>

            <div id="customerResults" aria-live="polite">
                <div class="table-summary">
                    Total customers:
                    <strong>{{ number_format($customers->total()) }}</strong>
                </div>

                <div class="table-wrap customers-table-wrap">
                    <table class="customers-table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Primary contact</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Contacts</th>
                                <th>Address</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($customers as $customer)
                                @php($primary = $customer->primaryContact)

                                <tr>
                                    <td>
                                        <span class="item-name">{{ $customer->name }}</span>
                                        <div class="small-muted">
                                            CUS-{{ str_pad((string) $customer->customer_id, 4, '0', STR_PAD_LEFT) }}
                                        </div>
                                    </td>

                                    <td>
                                        {{ $primary?->name ?? ($customer->contact_person ?? 'Not provided') }}
                                    </td>

                                    <td>
                                        @php($customerEmail = $primary?->email ?? $customer->email)

                                        @if ($customerEmail)
                                            <a class="customer-email" href="mailto:{{ $customerEmail }}">
                                                {{ $customerEmail }}
                                            </a>
                                        @else
                                            Not provided
                                        @endif
                                    </td>

                                    <td>
                                        @php($customerPhone = $primary?->phone ?? $customer->phone)

                                        @if ($customerPhone)
                                            <a class="customer-phone"
                                                href="tel:{{ preg_replace('/[^0-9+]/', '', $customerPhone) }}">
                                                {{ $customerPhone }}
                                            </a>
                                        @else
                                            Not provided
                                        @endif
                                    </td>

                                    <td>{{ number_format($customer->contacts_count ?? 0) }}</td>

                                    <td>
                                        <span class="customer-address" title="{{ $customer->address ?: 'Not provided' }}">
                                            {{ $customer->address ?: 'Not provided' }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="pill {{ $customer->status === 'Inactive' ? 'gray' : 'green' }}">
                                            {{ $customer->status }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="table-actions">
                                            @if (auth()->user()->hasPermission('customers.view'))
                                                <a class="action-btn action-view"
                                                    href="{{ route('customers.show', $customer) }}">
                                                    View
                                                </a>
                                            @endif

                                            @if (auth()->user()->hasPermission('customers.edit'))
                                                <a class="action-btn action-edit"
                                                    href="{{ route('customers.edit', $customer) }}">
                                                    Edit
                                                </a>
                                            @endif
                                            @if (auth()->user()->hasPermission('customers.deactivate'))
                                                @if ($customer->status === 'Inactive')
                                                    <form action="{{ route('customers.activate', $customer) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Activate this customer?');">
                                                        @csrf
                                                        @method('PATCH')

                                                        <button class="action-btn action-view" type="submit">
                                                            Activate
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('customers.deactivate', $customer) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Deactivate this customer?');">
                                                        @csrf
                                                        @method('PATCH')

                                                        <button class="action-btn action-delete" type="submit">
                                                            Deactivate
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">
                                        <div class="empty-state">
                                            <strong>No customers found.</strong>

                                            @if (request()->filled('search') || request()->filled('status'))
                                                <p>Try changing or clearing the current filters.</p>

                                                <a class="btn" href="{{ route('customersPage') }}">
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

                @if ($customers->hasPages())
                    <div class="pagination-wrapper">
                        {{ $customers->links('vendor.pagination.custom') }}
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .customers-page,
        .customers-card {
            min-width: 0;
        }

        .customer-filter-form {
            align-items: center;
        }

        .customer-search-group {
            display: flex;
            align-items: center;
            flex: 1 1 620px;
            gap: 6px;
            min-width: 0;
        }

        .customer-search-group .search {
            flex: 1 1 320px;
            width: 100%;
            min-width: 180px;
            max-width: 280px;
        }

        .customer-status-filter {
            flex: 0 0 auto;
        }

        .customer-status-filter .filter {
            min-width: 145px;
        }

        .customer-filter-progress {
            margin: -4px 0 12px;
            color: var(--green2);
            font-size: 10px;
            font-weight: 750;
        }

        .customer-search-button:disabled,
        .customer-status-filter .filter:disabled {
            cursor: wait;
            opacity: .65;
        }

        #customerResults {
            position: relative;
            transition: opacity .15s ease;
        }

        #customerResults.is-filtering {
            opacity: .55;
            pointer-events: none;
        }

        .customer-filter-progress[hidden] {
            display: none;
        }

        .customers-table-wrap {
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .customers-table {
            min-width: 1050px;
        }

        .customers-table th:last-child {
            text-align: center;
        }

        .customers-table td:last-child {
            text-align: right;
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
            .customers-page {
                padding-right: 14px;
                padding-left: 14px;
            }

            .customers-pagehead {
                align-items: stretch;
                flex-direction: column;
            }

            .customers-pagehead .actions,
            .customers-pagehead .btn {
                width: 100%;
            }

            .customers-card {
                padding: 15px;
            }

            .customer-filter-form {
                align-items: stretch;
                flex-direction: column;
            }

            .customer-search-group {
                display: grid;
                grid-template-columns: minmax(0, 1fr) auto;
                width: 100%;
                flex-basis: auto;
            }

            .customer-search-group .search {
                width: 100%;
                min-width: 0;
                max-width: none;
            }

            .customer-clear-button {
                grid-column: 1 / -1;
                width: 100%;
            }

            .customer-status-filter,
            .customer-status-filter .filter {
                width: 100%;
            }

            .table-summary {
                justify-content: flex-start;
            }
        }

        @media (max-width: 420px) {
            .customer-search-group {
                grid-template-columns: 1fr;
            }

            .customer-search-button,
            .customer-clear-button {
                grid-column: auto;
                width: 100%;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('js/customer-auto-filter.js') }}" defer></script>
@endpush
