@extends('layouts.master')

@section('title', 'Edit User | Golden Pride')
@section('page-name', 'Users / Edit')

@section('content')
    @php
        $selectedProjectIds = collect(old('project_ids', $selectedProjectIds ?? []))
            ->map(fn ($id): int => (int) $id)
            ->all();

        $selectedCustomerIds = collect(old('customer_ids', $selectedCustomerIds ?? []))
            ->map(fn ($id): int => (int) $id)
            ->all();

        $selectedSupplierIds = collect(old('supplier_ids', $selectedSupplierIds ?? []))
            ->map(fn ($id): int => (int) $id)
            ->all();
    @endphp

    <section class="content user-edit-page">
        <div class="pagehead user-edit-pagehead">
            <div>
                <div class="eyebrow">Directory</div>
                <h1>Edit User</h1>
                <p class="sub">
                    Update the user profile, role, account status, and assigned record access.
                </p>
            </div>

            <div class="actions">
                <a class="btn" href="{{ route('usersPage') }}">
                    &larr; Back
                </a>
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

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>Please correct the following fields:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card user-edit-card">
            <form
                action="{{ route('users.update', $user) }}"
                method="POST"
                id="userEditForm"
            >
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <div class="field">
                        <label for="name">Full name</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name', $user->name) }}"
                            maxlength="255"
                            autocomplete="name"
                            autofocus
                            required
                        >
                        @error('name')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="email">Email address</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email', $user->email) }}"
                            maxlength="255"
                            autocomplete="email"
                        >
                        @error('email')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="phone">Phone number</label>
                        <input
                            id="phone"
                            name="phone"
                            type="tel"
                            value="{{ old('phone', $user->phone) }}"
                            maxlength="30"
                            autocomplete="tel"
                            inputmode="tel"
                            placeholder="e.g. 09 123 456 789"
                        >
                        @error('phone')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="role_id">Role</label>
                        <select id="role_id" name="role_id" required>
                            <option value="">Select role</option>

                            @foreach ($roles as $role)
                                <option
                                    value="{{ $role->role_id }}"
                                    data-project-scope="{{ $role->project_access_scope }}"
                                    data-customer-scope="{{ $role->customer_access_scope }}"
                                    data-supplier-scope="{{ $role->supplier_access_scope }}"
                                    @selected(
                                        (string) old('role_id', $user->role_id)
                                        === (string) $role->role_id
                                    )
                                >
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="password">New password</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="new-password"
                            minlength="8"
                            placeholder="Leave blank to keep current password"
                        >
                        <small class="small-muted">
                            Leave blank unless you want to change the password.
                        </small>
                        @error('password')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="password_confirmation">Confirm new password</label>
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            autocomplete="new-password"
                            minlength="8"
                        >
                    </div>

                    <div class="field">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <option value="Active" @selected(old('status', $user->status) === 'Active')>
                                Active
                            </option>
                            <option value="Inactive" @selected(old('status', $user->status) === 'Inactive')>
                                Inactive
                            </option>
                        </select>
                        @error('status')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <section class="user-assignment-panel" id="recordAssignmentPanel">
                    <div class="user-assignment-heading">
                        <div>
                            <span class="user-assignment-kicker">Assigned access</span>
                            <h2>Assign accessible records</h2>
                            <p>
                                Lists appear only when the selected role uses Assigned access
                                for the corresponding module.
                            </p>
                        </div>
                    </div>

                    <div class="user-assignment-empty" id="assignmentEmptyState" hidden>
                        The selected role does not require individual record assignments.
                    </div>

                    <div class="user-assignment-grid">
                        <div class="assignment-group" data-scope-group="project" hidden>
                            <div class="assignment-group-heading">
                                <div>
                                    <strong>Projects</strong>
                                    <span id="projectAssignmentCount">0 selected</span>
                                </div>
                                <div class="assignment-group-actions">
                                    <button type="button" data-select-group="project">Select all</button>
                                    <button type="button" data-clear-group="project">Clear</button>
                                </div>
                            </div>

                            <label class="assignment-search-label" for="projectAssignmentSearch">
                                Search projects
                            </label>
                            <input
                                class="assignment-search"
                                id="projectAssignmentSearch"
                                type="search"
                                placeholder="Search projects..."
                                data-search-group="project"
                                autocomplete="off"
                            >

                            <div class="assignment-list" data-list-group="project">
                                @forelse ($projects as $project)
                                    <label class="assignment-option" data-assignment-option>
                                        <input
                                            type="checkbox"
                                            name="project_ids[]"
                                            value="{{ $project->project_id }}"
                                            data-assignment-checkbox="project"
                                            @checked(
                                                in_array(
                                                    (int) $project->project_id,
                                                    $selectedProjectIds,
                                                    true
                                                )
                                            )
                                        >
                                        <span>
                                            <strong>{{ $project->name }}</strong>
                                            <small>
                                                PRJ-{{ str_pad((string) $project->project_id, 4, '0', STR_PAD_LEFT) }}
                                            </small>
                                        </span>
                                    </label>
                                @empty
                                    <span class="assignment-no-records">No projects available.</span>
                                @endforelse
                            </div>

                            @error('project_ids')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                            @error('project_ids.*')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="assignment-group" data-scope-group="customer" hidden>
                            <div class="assignment-group-heading">
                                <div>
                                    <strong>Customers</strong>
                                    <span id="customerAssignmentCount">0 selected</span>
                                </div>
                                <div class="assignment-group-actions">
                                    <button type="button" data-select-group="customer">Select all</button>
                                    <button type="button" data-clear-group="customer">Clear</button>
                                </div>
                            </div>

                            <label class="assignment-search-label" for="customerAssignmentSearch">
                                Search customers
                            </label>
                            <input
                                class="assignment-search"
                                id="customerAssignmentSearch"
                                type="search"
                                placeholder="Search customers..."
                                data-search-group="customer"
                                autocomplete="off"
                            >

                            <div class="assignment-list" data-list-group="customer">
                                @forelse ($customers as $customer)
                                    <label class="assignment-option" data-assignment-option>
                                        <input
                                            type="checkbox"
                                            name="customer_ids[]"
                                            value="{{ $customer->customer_id }}"
                                            data-assignment-checkbox="customer"
                                            @checked(
                                                in_array(
                                                    (int) $customer->customer_id,
                                                    $selectedCustomerIds,
                                                    true
                                                )
                                            )
                                        >
                                        <span>
                                            <strong>{{ $customer->name }}</strong>
                                            <small>
                                                CUS-{{ str_pad((string) $customer->customer_id, 4, '0', STR_PAD_LEFT) }}
                                            </small>
                                        </span>
                                    </label>
                                @empty
                                    <span class="assignment-no-records">No customers available.</span>
                                @endforelse
                            </div>

                            @error('customer_ids')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                            @error('customer_ids.*')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="assignment-group" data-scope-group="supplier" hidden>
                            <div class="assignment-group-heading">
                                <div>
                                    <strong>Suppliers</strong>
                                    <span id="supplierAssignmentCount">0 selected</span>
                                </div>
                                <div class="assignment-group-actions">
                                    <button type="button" data-select-group="supplier">Select all</button>
                                    <button type="button" data-clear-group="supplier">Clear</button>
                                </div>
                            </div>

                            <label class="assignment-search-label" for="supplierAssignmentSearch">
                                Search suppliers
                            </label>
                            <input
                                class="assignment-search"
                                id="supplierAssignmentSearch"
                                type="search"
                                placeholder="Search suppliers..."
                                data-search-group="supplier"
                                autocomplete="off"
                            >

                            <div class="assignment-list" data-list-group="supplier">
                                @forelse ($suppliers as $supplier)
                                    <label class="assignment-option" data-assignment-option>
                                        <input
                                            type="checkbox"
                                            name="supplier_ids[]"
                                            value="{{ $supplier->supplier_id }}"
                                            data-assignment-checkbox="supplier"
                                            @checked(
                                                in_array(
                                                    (int) $supplier->supplier_id,
                                                    $selectedSupplierIds,
                                                    true
                                                )
                                            )
                                        >
                                        <span>
                                            <strong>{{ $supplier->name }}</strong>
                                            <small>
                                                SUP-{{ str_pad((string) $supplier->supplier_id, 4, '0', STR_PAD_LEFT) }}
                                            </small>
                                        </span>
                                    </label>
                                @empty
                                    <span class="assignment-no-records">No suppliers available.</span>
                                @endforelse
                            </div>

                            @error('supplier_ids')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                            @error('supplier_ids.*')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </section>

                <div class="form-footer">
                    <a class="btn" href="{{ route('usersPage') }}">Cancel</a>
                    <button class="btn btn-primary" type="submit">Save changes</button>
                </div>
            </form>

            @if (
                auth()->user()->hasPermission('users.edit')
                || auth()->user()->hasPermission('users.manage')
            )
                <div class="user-password-reset-section">
                    <div>
                        <span class="user-password-reset-kicker">Account security</span>
                        <h2>Reset password</h2>
                        <p>
                            Reset the account password to
                            <strong>Default@12345</strong>.
                        </p>
                    </div>

                    <form
                        action="{{ route('users.reset-password', $user) }}"
                        method="POST"
                        onsubmit="return confirm('Reset this user password to Default@12345?');"
                    >
                        @csrf
                        @method('PATCH')
                        <button class="btn user-reset-password-button" type="submit">
                            Reset password
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .user-edit-page,
        .user-edit-card {
            min-width: 0;
        }

        .user-assignment-panel {
            margin-top: 24px;
            padding: 20px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #fafcfb;
        }

        .user-assignment-kicker {
            display: block;
            margin-bottom: 4px;
            color: var(--green2);
            font-size: 9px;
            font-weight: 850;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .user-assignment-heading h2 {
            margin: 0;
            color: var(--ink);
            font-size: 16px;
        }

        .user-assignment-heading p {
            max-width: 720px;
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 10px;
            line-height: 1.55;
        }

        .user-assignment-empty {
            margin-top: 16px;
            padding: 12px;
            border: 1px dashed var(--line);
            border-radius: 9px;
            color: var(--muted);
            background: var(--white);
            font-size: 10px;
        }

        .user-assignment-empty[hidden],
        .assignment-group[hidden] {
            display: none;
        }

        .user-assignment-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-top: 16px;
        }

        .assignment-group {
            min-width: 0;
            padding: 13px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: var(--white);
        }

        .assignment-group-heading {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 10px;
        }

        .assignment-group-heading strong {
            display: block;
            color: var(--ink);
            font-size: 11px;
        }

        .assignment-group-heading span {
            display: block;
            margin-top: 2px;
            color: var(--green2);
            font-size: 9px;
            font-weight: 750;
        }

        .assignment-group-actions {
            display: flex;
            gap: 5px;
        }

        .assignment-group-actions button {
            padding: 4px 7px;
            border: 1px solid var(--line);
            border-radius: 6px;
            background: #f8faf8;
            color: var(--green);
            font-size: 8px;
            font-weight: 800;
            cursor: pointer;
        }

        .assignment-search-label {
            position: absolute;
            width: 1px;
            height: 1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
        }

        .assignment-search {
            width: 100%;
            margin-bottom: 9px;
            min-height: 36px;
            font-size: 10px;
        }

        .assignment-list {
            display: grid;
            gap: 6px;
            max-height: 245px;
            overflow-y: auto;
        }

        .assignment-option {
            display: flex;
            align-items: center;
            gap: 9px;
            min-width: 0;
            padding: 8px;
            border: 1px solid transparent;
            border-radius: 7px;
            cursor: pointer;
        }

        .assignment-option:hover {
            border-color: var(--line);
            background: #f6f9f7;
        }

        .assignment-option:has(input:checked) {
            border-color: #b7d0c2;
            background: var(--mint);
        }

        .assignment-option input {
            flex: 0 0 16px;
            width: 16px;
            height: 16px;
        }

        .assignment-option > span {
            min-width: 0;
        }

        .assignment-option strong,
        .assignment-option small {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .assignment-option strong {
            color: var(--ink);
            font-size: 10px;
        }

        .assignment-option small {
            margin-top: 2px;
            color: var(--muted);
            font-size: 8px;
        }

        .assignment-option.is-filtered-out {
            display: none;
        }

        .assignment-no-records {
            padding: 10px;
            color: var(--muted);
            font-size: 9px;
        }

        .user-password-reset-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            margin-top: 24px;
            padding: 18px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #f8faf8;
        }

        .user-password-reset-section h2 {
            margin: 0;
            color: var(--ink);
            font-size: 15px;
        }

        .user-password-reset-section p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 11px;
        }

        .user-password-reset-kicker {
            display: block;
            margin-bottom: 5px;
            color: var(--green2);
            font-size: 9px;
            font-weight: 850;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .user-password-reset-section form {
            margin: 0;
        }

        .user-reset-password-button {
            border-color: #e5c5a0;
            background: #fff8ec;
            color: #805817;
        }

        @media (max-width: 980px) {
            .user-assignment-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 620px) {
            .user-edit-pagehead {
                align-items: stretch;
                flex-direction: column;
            }

            .user-edit-pagehead .actions,
            .user-edit-pagehead .btn {
                width: 100%;
            }

            .user-assignment-panel {
                padding: 14px;
            }

            .assignment-group-heading {
                align-items: stretch;
                flex-direction: column;
            }

            .assignment-group-actions {
                display: grid;
                grid-template-columns: 1fr 1fr;
            }

            .user-password-reset-section {
                align-items: stretch;
                flex-direction: column;
            }

            .user-password-reset-section form,
            .user-password-reset-section button {
                width: 100%;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('userEditForm');
            const roleSelect = document.getElementById('role_id');
            const emptyState = document.getElementById('assignmentEmptyState');

            if (!form || !roleSelect) {
                return;
            }

            const groups = {
                project: form.querySelector('[data-scope-group="project"]'),
                customer: form.querySelector('[data-scope-group="customer"]'),
                supplier: form.querySelector('[data-scope-group="supplier"]'),
            };

            function getCheckboxes(groupName) {
                return Array.from(
                    form.querySelectorAll(
                        '[data-assignment-checkbox="' + groupName + '"]'
                    )
                );
            }

            function updateCount(groupName) {
                const countElement = document.getElementById(
                    groupName + 'AssignmentCount'
                );

                if (!countElement) {
                    return;
                }

                const selectedCount = getCheckboxes(groupName)
                    .filter(function (checkbox) {
                        return checkbox.checked;
                    }).length;

                countElement.textContent = selectedCount + ' selected';
            }

            function refreshVisibleGroups() {
                const selectedOption = roleSelect.options[
                    roleSelect.selectedIndex
                ];

                const scopes = {
                    project: selectedOption?.dataset.projectScope || '',
                    customer: selectedOption?.dataset.customerScope || '',
                    supplier: selectedOption?.dataset.supplierScope || '',
                };

                let visibleGroupCount = 0;

                Object.keys(groups).forEach(function (groupName) {
                    const group = groups[groupName];
                    const visible = scopes[groupName] === 'assigned';

                    if (group) {
                        group.hidden = !visible;
                    }

                    if (visible) {
                        visibleGroupCount += 1;
                    }

                    updateCount(groupName);
                });

                if (emptyState) {
                    emptyState.hidden = visibleGroupCount > 0;
                }
            }

            Object.keys(groups).forEach(function (groupName) {
                getCheckboxes(groupName).forEach(function (checkbox) {
                    checkbox.addEventListener('change', function () {
                        updateCount(groupName);
                    });
                });
            });

            form.querySelectorAll('[data-select-group]').forEach(
                function (button) {
                    button.addEventListener('click', function () {
                        const groupName = button.dataset.selectGroup;

                        getCheckboxes(groupName).forEach(function (checkbox) {
                            const option = checkbox.closest(
                                '[data-assignment-option]'
                            );

                            if (!option?.classList.contains('is-filtered-out')) {
                                checkbox.checked = true;
                            }
                        });

                        updateCount(groupName);
                    });
                }
            );

            form.querySelectorAll('[data-clear-group]').forEach(
                function (button) {
                    button.addEventListener('click', function () {
                        const groupName = button.dataset.clearGroup;

                        getCheckboxes(groupName).forEach(function (checkbox) {
                            checkbox.checked = false;
                        });

                        updateCount(groupName);
                    });
                }
            );

            form.querySelectorAll('[data-search-group]').forEach(
                function (searchInput) {
                    searchInput.addEventListener('input', function () {
                        const groupName = searchInput.dataset.searchGroup;
                        const searchValue = searchInput.value
                            .trim()
                            .toLowerCase();

                        const list = form.querySelector(
                            '[data-list-group="' + groupName + '"]'
                        );

                        list?.querySelectorAll(
                            '[data-assignment-option]'
                        ).forEach(function (option) {
                            option.classList.toggle(
                                'is-filtered-out',
                                !option.textContent
                                    .toLowerCase()
                                    .includes(searchValue)
                            );
                        });
                    });
                }
            );

            roleSelect.addEventListener('change', refreshVisibleGroups);
            refreshVisibleGroups();
        });
    </script>
@endpush
