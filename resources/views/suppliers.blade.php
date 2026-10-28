@extends('layouts.master')
@section('title', 'Suppliers | Golden Pride')
@section('page-name', 'Suppliers')
@section('content')
    <section class="content">
        <div class="pagehead">
            <div>
                <div class="eyebrow">Directory</div>
                <h1>Suppliers</h1>
                <p class="sub">Manage supplier records and contact persons.</p>
            </div>
            <div class="actions">
                @if (auth()->user()->hasPermission('suppliers.create'))
                    <a class="btn btn-primary" href="{{ route('suppliers.create') }}">＋ Create supplier</a>
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
            <form class="toolbar" action="{{ route('suppliersPage') }}" method="GET">
                <div class="supplier-search-group"><input class="search" name="search" type="search"
                        value="{{ request('search') }}" placeholder="Search suppliers or contacts..."><button class="btn"
                        type="submit">Search</button>
                    @if (request('search') || request('status'))
                        <a class="btn" href="{{ route('suppliersPage') }}">Clear</a>
                    @endif
                </div>
                <select class="filter" name="status" onchange="this.form.submit()">
                    <option value="">All records</option>
                    <option value="Active" @selected(request('status') === 'Active')>Active</option>
                    <option value="Inactive" @selected(request('status') === 'Inactive')>Inactive</option>
                </select>
            </form>
            <div class="table-summary">Total suppliers: <strong>{{ $suppliers->total() }}</strong></div>
            <div class="table-wrap">
                <table>
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
                                <td><span class="item-name">{{ $supplier->name }}</span>
                                    <div class="small-muted">
                                        SUP-{{ str_pad((string) $supplier->supplier_id, 4, '0', STR_PAD_LEFT) }}</div>
                                </td>
                                <td>{{ $primary?->name ?? ($supplier->contact_person ?? 'Not provided') }}</td>
                                <td>{{ $supplier->category ?: 'Not provided' }}</td>
                                <td>{{ $primary?->email ?? ($supplier->email ?? 'Not provided') }}</td>
                                <td>{{ $primary?->phone ?? ($supplier->phone ?? 'Not provided') }}</td>
                                <td>{{ $supplier->contacts_count ?? 0 }}</td>
                                <td><span
                                        class="pill {{ $supplier->status === 'Inactive' ? 'gray' : 'green' }}">{{ $supplier->status }}</span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        @if (auth()->user()->hasPermission('suppliers.view'))
                                            <a class="action-btn action-view"
                                                href="{{ route('suppliers.show', $supplier) }}">View</a>
                                        @endif
                                        @if (auth()->user()->hasPermission('suppliers.edit'))
                                            <a class="action-btn action-edit"
                                                href="{{ route('suppliers.edit', $supplier) }}">Edit</a>
                                        @endif
                                        @if (auth()->user()->hasPermission('suppliers.deactivate') && $supplier->status !== 'Inactive')
                                            <form action="{{ route('suppliers.deactivate', $supplier) }}" method="POST"
                                                onsubmit="return confirm('Deactivate this supplier?');">@csrf
                                                @method('PATCH')<button class="action-btn action-delete"
                                                    type="submit">Deactivate</button></form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty<tr>
                                <td colspan="8">
                                    <div class="empty-state"><strong>No suppliers found.</strong></div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($suppliers->hasPages())
                <div class="pagination-wrapper">{{ $suppliers->links('vendor.pagination.custom') }}</div>
            @endif
        </div>
    </section>
@endsection
