@extends('layouts.master')

@section('title', $customer->name . ' | Golden Pride')
@section('page-name', 'Customers / View')

@section('content')
    @php
        $primaryContact = $customer->contacts->firstWhere('is_primary', true);
    @endphp

    <section class="content customer-detail-page">
        <div class="pagehead">
            <div>
                <div class="eyebrow">
                    CUS-{{ str_pad((string) $customer->customer_id, 4, '0', STR_PAD_LEFT) }}
                </div>

                <h1>{{ $customer->name }}</h1>

                <p class="sub">
                    View customer information and all associated contact persons.
                </p>
            </div>

            <div class="actions">
                <a class="btn" href="{{ route('customersPage') }}">
                    &larr; Back to customers
                </a>

                @if (auth()->user()->hasPermission('customers.edit'))
                    <a class="btn btn-primary" href="{{ route('customers.edit', $customer) }}">
                        Edit customer
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

        <div class="customer-detail-grid">
            <section class="card customer-detail-card">
                <div class="customer-detail-heading">
                    <div>
                        <span class="customer-detail-kicker">Customer profile</span>
                        <h2>{{ $customer->name }}</h2>
                    </div>

                    <span class="pill {{ $customer->status === 'Inactive' ? 'gray' : 'green' }}">
                        {{ $customer->status }}
                    </span>
                </div>

                <dl class="customer-detail-list">
                    <div>
                        <dt>Customer ID</dt>
                        <dd>
                            CUS-{{ str_pad((string) $customer->customer_id, 4, '0', STR_PAD_LEFT) }}
                        </dd>
                    </div>

                    <div>
                        <dt>Customer name</dt>
                        <dd>{{ $customer->name }}</dd>
                    </div>

                    <div>
                        <dt>Primary contact</dt>
                        <dd>
                            {{ $primaryContact?->name ?? $customer->contact_person ?? 'Not provided' }}
                        </dd>
                    </div>

                    <div>
                        <dt>Primary email</dt>
                        <dd>
                            @php($primaryEmail = $primaryContact?->email ?? $customer->email)

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
                            @php($primaryPhone = $primaryContact?->phone ?? $customer->phone)

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
                        <dd>{{ $customer->contacts->count() }}</dd>
                    </div>

                    <div class="customer-detail-full-row">
                        <dt>Address</dt>
                        <dd>{{ $customer->address ?: 'Not provided' }}</dd>
                    </div>

                    <div class="customer-detail-full-row">
                        <dt>Notes</dt>
                        <dd class="customer-detail-notes">
                            {{ $customer->notes ?: 'No notes provided.' }}
                        </dd>
                    </div>
                </dl>
            </section>

            <section class="card customer-detail-card">
                <div class="card-title">
                    <h2>Record information</h2>
                </div>

                <dl class="customer-detail-list customer-record-list">
                    <div>
                        <dt>Created</dt>
                        <dd>
                            {{ $customer->created_at?->format('M d, Y h:i A') ?? 'Not available' }}
                        </dd>
                    </div>

                    <div>
                        <dt>Last updated</dt>
                        <dd>
                            {{ $customer->updated_at?->format('M d, Y h:i A') ?? 'Not available' }}
                        </dd>
                    </div>

                    <div>
                        <dt>Updated relative</dt>
                        <dd>
                            {{ $customer->updated_at?->diffForHumans() ?? 'Not available' }}
                        </dd>
                    </div>
                </dl>

                <div class="customer-detail-actions">
                    @if (auth()->user()->hasPermission('customers.edit'))
                        <a class="btn btn-soft" href="{{ route('customers.edit', $customer) }}">
                            Edit customer information
                        </a>
                    @endif

                    @if (
                        auth()->user()->hasPermission('customers.deactivate')
                        && $customer->status !== 'Inactive'
                    )
                        <form
                            action="{{ route('customers.deactivate', $customer) }}"
                            method="POST"
                            onsubmit="return confirm('Deactivate this customer?');"
                        >
                            @csrf
                            @method('PATCH')

                            <button class="btn customer-detail-deactivate" type="submit">
                                Deactivate customer
                            </button>
                        </form>
                    @endif
                </div>
            </section>

            <section class="card customer-contacts-detail-card">
                <div class="customer-contacts-detail-heading">
                    <div>
                        <span class="customer-detail-kicker">Directory</span>
                        <h2>Contact Persons</h2>
                        <p>All contacts associated with this customer.</p>
                    </div>

                    <span class="customer-contact-count">
                        {{ $customer->contacts->count() }} contacts
                    </span>
                </div>

                @if ($customer->contacts->isNotEmpty())
                    <div class="customer-contact-table-wrap">
                        <table class="customer-contact-table">
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
                                @foreach ($customer->contacts as $contact)
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
                                                <span class="customer-primary-badge">Primary</span>
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
                        No contact persons are associated with this customer.
                    </div>
                @endif
            </section>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .customer-detail-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(280px, .8fr);
            gap: 18px;
        }

        .customer-detail-card,
        .customer-contacts-detail-card {
            min-width: 0;
        }

        .customer-detail-heading,
        .customer-contacts-detail-heading {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--line);
        }

        .customer-detail-heading h2,
        .customer-contacts-detail-heading h2 {
            margin: 4px 0 0;
            color: var(--ink);
            font-size: 17px;
        }

        .customer-contacts-detail-heading p {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 11px;
        }

        .customer-detail-kicker {
            color: var(--green2);
            font-size: 9px;
            font-weight: 850;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .customer-detail-list {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0 22px;
            margin: 0;
        }

        .customer-detail-list > div {
            padding: 13px 0;
            border-bottom: 1px solid #edf0ec;
        }

        .customer-detail-list dt {
            margin-bottom: 5px;
            color: var(--muted);
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .customer-detail-list dd {
            margin: 0;
            color: var(--ink);
            font-size: 11px;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .customer-detail-list dd a,
        .customer-contact-table a {
            color: var(--green2);
        }

        .customer-detail-list dd a:hover,
        .customer-contact-table a:hover {
            text-decoration: underline;
        }

        .customer-detail-full-row {
            grid-column: 1 / -1;
        }

        .customer-detail-notes {
            line-height: 1.7;
            white-space: pre-line;
        }

        .customer-record-list {
            grid-template-columns: 1fr;
        }

        .customer-detail-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 18px;
            padding-top: 18px;
            border-top: 1px solid var(--line);
        }

        .customer-detail-actions form {
            margin: 0;
        }

        .customer-detail-deactivate {
            border-color: #efb0ac;
            background: #fff0ef;
            color: var(--red);
        }

        .customer-contacts-detail-card {
            grid-column: 1 / -1;
            padding: 20px;
            border: 1px solid var(--line);
            border-radius: 16px;
            background: var(--white);
            box-shadow: var(--shadow);
        }

        .customer-contact-count,
        .customer-primary-badge {
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

        .customer-contact-table-wrap {
            overflow-x: auto;
        }

        .customer-contact-table {
            min-width: 760px;
        }

        .customer-contact-table td strong {
            color: var(--ink);
        }

        @media (max-width: 900px) {
            .customer-detail-grid,
            .customer-detail-list {
                grid-template-columns: 1fr;
            }

            .customer-contacts-detail-card,
            .customer-detail-full-row {
                grid-column: auto;
            }
        }

        @media (max-width: 620px) {
            .customer-detail-heading,
            .customer-contacts-detail-heading {
                flex-direction: column;
            }

            .customer-detail-actions {
                flex-direction: column;
            }

            .customer-detail-actions .btn,
            .customer-detail-actions form,
            .customer-detail-actions button {
                width: 100%;
            }
        }
    </style>
@endpush
