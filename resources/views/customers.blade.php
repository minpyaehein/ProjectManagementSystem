@extends('layouts.master')

@section('title', 'Customers | Golden Pride')
@section('page-name', 'Customers')

@section('content')
    <section class="content">
        <div class="pagehead">
            <div>
                <div class="eyebrow">Directory</div>
                <h1>Customers</h1>
                <p class="sub">Manage customer records and contact persons.</p>
            </div>
            <div class="actions">
                @if (auth()->user()->hasPermission('customers.create'))
                    <a class="btn btn-primary" href="{{ route('customers.create') }}">＋ Create customer</a>
                @endif
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <form class="toolbar" action="{{ route('customersPage') }}" method="GET">
                <div class="customer-search-group">
                    <input class="search" name="search" type="search" value="{{ request('search') }}"
                        placeholder="Search customers or contacts...">
                    <button class="btn" type="submit">Search</button>
                    @if (request('search') || request('status'))
                        <a class="btn" href="{{ route('customersPage') }}">Clear</a>
                    @endif
                </div>
                <select class="filter" name="status" onchange="this.form.submit()">
                    <option value="">All records</option>
                    <option value="Active" @selected(request('status') === 'Active')>Active</option>
                    <option value="Inactive" @selected(request('status') === 'Inactive')>Inactive</option>
                </select>
            </form>

            <div class="table-summary">Total customers: <strong>{{ $customers->total() }}</strong></div>

            <div class="table-wrap">
                <table>
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
                                        CUS-{{ str_pad((string) $customer->customer_id, 4, '0', STR_PAD_LEFT) }}</div>
                                </td>
                                <td>{{ $primary?->name ?? ($customer->contact_person ?? 'Not provided') }}</td>
                                <td>{{ $primary?->email ?? ($customer->email ?? 'Not provided') }}</td>
                                <td>{{ $primary?->phone ?? ($customer->phone ?? 'Not provided') }}</td>
                                <td>{{ $customer->contacts_count ?? 0 }}</td>
                                <td><span class="customer-address">{{ $customer->address ?: 'Not provided' }}</span></td>
                                <td><span
                                        class="pill {{ $customer->status === 'Inactive' ? 'gray' : 'green' }}">{{ $customer->status }}</span>
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
                                                href="{{ route('customers.edit', $customer) }}">Edit</a>
                                        @endif
                                        @if (auth()->user()->hasPermission('customers.deactivate') && $customer->status !== 'Inactive')
                                            <form action="{{ route('customers.deactivate', $customer) }}" method="POST"
                                                onsubmit="return confirm('Deactivate this customer?');">
                                                @csrf
                                                @method('PATCH')
                                                <button class="action-btn action-delete" type="submit">Deactivate</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state"><strong>No customers found.</strong></div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($customers->hasPages())
                <div class="pagination-wrapper">{{ $customers->links('vendor.pagination.custom') }}</div>
            @endif
        </div>
    </section>
@endsection
