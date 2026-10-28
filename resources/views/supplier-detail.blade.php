@extends('layouts.master')

@section('title', $supplier->name . ' | Golden Pride')
@section('page-name', 'Suppliers / View')

@section('content')
    @php
        $primaryContact = $supplier->contacts->firstWhere('is_primary', true);
    @endphp

    <section class="content supplier-detail-page">
        <div class="pagehead">
            <div>
                <div class="eyebrow">
                    CUS-{{ str_pad((string) $supplier->supplier_id, 4, '0', STR_PAD_LEFT) }}
                </div>

                <h1>{{ $supplier->name }}</h1>

                <p class="sub">
                    View supplier information and all associated contact persons.
                </p>
            </div>

            <div class="actions">
                <a class="btn" href="{{ route('suppliersPage') }}">
                    &larr; Back to suppliers
                </a>

                @if (auth()->user()->hasPermission('suppliers.edit'))
                    <a class="btn btn-primary" href="{{ route('suppliers.edit', $supplier) }}">
                        Edit supplier
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

        <div class="supplier-detail-grid">
            <section class="card supplier-detail-card">
                <div class="supplier-detail-heading">
                    <div>
                        <span class="supplier-detail-kicker">Supplier profile</span>
                        <h2>{{ $supplier->name }}</h2>
                    </div>

                    <span class="pill {{ $supplier->status === 'Inactive' ? 'gray' : 'green' }}">
                        {{ $supplier->status }}
                    </span>
                </div>

                <dl class="supplier-detail-list">
                    <div>
                        <dt>Supplier ID</dt>
                        <dd>
                            CUS-{{ str_pad((string) $supplier->supplier_id, 4, '0', STR_PAD_LEFT) }}
                        </dd>
                    </div>

                    <div>
                        <dt>Supplier name</dt>
                        <dd>{{ $supplier->name }}</dd>
                    </div>

                    <div>
                        <dt>Category</dt>
                        <dd>{{ $supplier->category ?: 'Not provided' }}</dd>
                    </div>

                    <div>
                        <dt>Primary contact</dt>
                        <dd>
                            {{ $primaryContact?->name ?? $supplier->contact_person ?? 'Not provided' }}
                        </dd>
                    </div>

                    <div>
                        <dt>Primary email</dt>
                        <dd>
                            @php($primaryEmail = $primaryContact?->email ?? $supplier->email)

                            @if ($primaryEmail)
                                <a href="mailto:{{ $primaryEmail }}">
                                    {{ $primaryEmail }}
                                </a>
                            @else
                                Not provided
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt>Primary phone</dt>
                        <dd>
                            @php($primaryPhone = $primaryContact?->phone ?? $supplier->phone)

                            @if ($primaryPhone)
                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $primaryPhone) }}">
                                    {{ $primaryPhone }}
                                </a>
                            @else
                                Not provided
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt>Total contacts</dt>
                        <dd>{{ $supplier->contacts->count() }}</dd>
                    </div>

                    <div class="supplier-detail-full-row">
                        <dt>Address</dt>
                        <dd>{{ $supplier->address ?: 'Not provided' }}</dd>
                    </div>

                    <div class="supplier-detail-full-row">
                        <dt>Notes</dt>
                        <dd class="supplier-detail-notes">
                            {{ $supplier->notes ?: 'No notes provided.' }}
                        </dd>
                    </div>
                </dl>
            </section>

            <section class="card supplier-detail-card">
                <div class="card-title">
                    <h2>Record information</h2>
                </div>

                <dl class="supplier-detail-list supplier-record-list">
                    <div>
                        <dt>Created</dt>
                        <dd>
                            {{ $supplier->created_at?->format('M d, Y h:i A') ?? 'Not available' }}
                        </dd>
                    </div>

                    <div>
                        <dt>Last updated</dt>
                        <dd>
                            {{ $supplier->updated_at?->format('M d, Y h:i A') ?? 'Not available' }}
                        </dd>
                    </div>

                    <div>
                        <dt>Updated relative</dt>
                        <dd>
                            {{ $supplier->updated_at?->diffForHumans() ?? 'Not available' }}
                        </dd>
                    </div>
                </dl>

                <div class="supplier-detail-actions">
                    @if (auth()->user()->hasPermission('suppliers.edit'))
                        <a class="btn btn-soft" href="{{ route('suppliers.edit', $supplier) }}">
                            Edit supplier information
                        </a>
                    @endif

                    @if (
                        auth()->user()->hasPermission('suppliers.deactivate')
                        && $supplier->status !== 'Inactive'
                    )
                        <form
                            action="{{ route('suppliers.deactivate', $supplier) }}"
                            method="POST"
                            onsubmit="return confirm('Deactivate this supplier?');"
                        >
                            @csrf
                            @method('PATCH')

                            <button class="btn supplier-detail-deactivate" type="submit">
                                Deactivate supplier
                            </button>
                        </form>
                    @endif
                </div>
            </section>

            <section class="card supplier-contacts-detail-card">
                <div class="supplier-contacts-detail-heading">
                    <div>
                        <span class="supplier-detail-kicker">Directory</span>
                        <h2>Contact Persons</h2>
                        <p>All contacts associated with this supplier.</p>
                    </div>

                    <span class="supplier-contact-count">
                        {{ $supplier->contacts->count() }} contacts
                    </span>
                </div>

                @if ($supplier->contacts->isNotEmpty())
                    <div class="supplier-contact-table-wrap">
                        <table class="supplier-contact-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Record</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($supplier->contacts as $contact)
                                    <tr>
                                        <td>
                                            <strong>{{ $contact->name }}</strong>
                                        </td>

                                        <td>{{ $contact->position ?: 'Not provided' }}</td>

                                        <td>
                                            @if ($contact->phone)
                                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $contact->phone) }}">
                                                    {{ $contact->phone }}
                                                </a>
                                            @else
                                                Not provided
                                            @endif
                                        </td>

                                        <td>
                                            @if ($contact->email)
                                                <a href="mailto:{{ $contact->email }}">
                                                    {{ $contact->email }}
                                                </a>
                                            @else
                                                Not provided
                                            @endif
                                        </td>

                                        <td>
                                            @if ($contact->is_primary)
                                                <span class="supplier-primary-badge">Primary</span>
                                            @else
                                                <span class="small-muted">Contact</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-note">
                        No contact persons are associated with this supplier.
                    </div>
                @endif
            </section>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .supplier-detail-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(280px, .8fr);
            gap: 18px;
        }

        .supplier-detail-card,
        .supplier-contacts-detail-card {
            min-width: 0;
        }

        .supplier-detail-heading,
        .supplier-contacts-detail-heading {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--line);
        }

        .supplier-detail-heading h2,
        .supplier-contacts-detail-heading h2 {
            margin: 4px 0 0;
            color: var(--ink);
            font-size: 17px;
        }

        .supplier-contacts-detail-heading p {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 11px;
        }

        .supplier-detail-kicker {
            color: var(--green2);
            font-size: 9px;
            font-weight: 850;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .supplier-detail-list {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0 22px;
            margin: 0;
        }

        .supplier-detail-list > div {
            padding: 13px 0;
            border-bottom: 1px solid #edf0ec;
        }

        .supplier-detail-list dt {
            margin-bottom: 5px;
            color: var(--muted);
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .supplier-detail-list dd {
            margin: 0;
            color: var(--ink);
            font-size: 11px;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .supplier-detail-list dd a,
        .supplier-contact-table a {
            color: var(--green2);
        }

        .supplier-detail-list dd a:hover,
        .supplier-contact-table a:hover {
            text-decoration: underline;
        }

        .supplier-detail-full-row {
            grid-column: 1 / -1;
        }

        .supplier-detail-notes {
            line-height: 1.7;
            white-space: pre-line;
        }

        .supplier-record-list {
            grid-template-columns: 1fr;
        }

        .supplier-detail-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 18px;
            padding-top: 18px;
            border-top: 1px solid var(--line);
        }

        .supplier-detail-actions form {
            margin: 0;
        }

        .supplier-detail-deactivate {
            border-color: #efb0ac;
            background: #fff0ef;
            color: var(--red);
        }

        .supplier-contacts-detail-card {
            grid-column: 1 / -1;
            padding: 20px;
            border: 1px solid var(--line);
            border-radius: 16px;
            background: var(--white);
            box-shadow: var(--shadow);
        }

        .supplier-contact-count,
        .supplier-primary-badge {
            display: inline-flex;
            align-items: center;
            min-height: 26px;
            padding: 0 9px;
            border-radius: 999px;
            background: var(--mint);
            color: var(--green);
            font-size: 9px;
            font-weight: 850;
            white-space: nowrap;
        }

        .supplier-contact-table-wrap {
            overflow-x: auto;
        }

        .supplier-contact-table {
            min-width: 760px;
        }

        .supplier-contact-table td strong {
            color: var(--ink);
        }

        @media (max-width: 900px) {
            .supplier-detail-grid,
            .supplier-detail-list {
                grid-template-columns: 1fr;
            }

            .supplier-contacts-detail-card,
            .supplier-detail-full-row {
                grid-column: auto;
            }
        }

        @media (max-width: 620px) {
            .supplier-detail-heading,
            .supplier-contacts-detail-heading {
                flex-direction: column;
            }

            .supplier-detail-actions {
                flex-direction: column;
            }

            .supplier-detail-actions .btn,
            .supplier-detail-actions form,
            .supplier-detail-actions button {
                width: 100%;
            }
        }
    </style>
@endpush
