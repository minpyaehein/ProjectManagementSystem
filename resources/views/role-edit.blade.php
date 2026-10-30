@extends('layouts.master')

@section('title', 'Edit Role | Golden Pride')
@section('page-name', 'Roles / Edit')

@section('content')
    @php
        $modules = [
            'projects' => 'Projects',
            'customers' => 'Customers',
            'suppliers' => 'Suppliers',
            'users' => 'Users',
            'roles' => 'Roles & Permissions',
        ];

        $moduleActions = [
            'projects' => [
                'view' => 'View',
                'create' => 'Create',
                'edit' => 'Edit',
            ],
            'customers' => [
                'view' => 'View',
                'create' => 'Create',
                'edit' => 'Edit',
                'deactivate' => 'Deactivate',
            ],
            'suppliers' => [
                'view' => 'View',
                'create' => 'Create',
                'edit' => 'Edit',
                'deactivate' => 'Deactivate',
            ],
            'users' => [
                'view' => 'View',
                'create' => 'Create',
                'edit' => 'Edit',
                'deactivate' => 'Deactivate',
            ],
            'roles' => [
                'view' => 'View',
                'create' => 'Create',
                'edit' => 'Edit',
                'deactivate' => 'Deactivate',
            ],
        ];

        $accessScopes = [
            'own' => 'Own Only',
            'assigned' => 'Assigned',
            'all' => 'All',
        ];

        $scopeFields = [
            'customer_access_scope' => [
                'label' => 'Customer access',
                'help' => 'Controls which customer records this role can access.',
            ],
            'supplier_access_scope' => [
                'label' => 'Supplier access',
                'help' => 'Controls which supplier records this role can access.',
            ],
            'project_access_scope' => [
                'label' => 'Project access',
                'help' => 'Controls which project records this role can access.',
            ],
        ];

        $permissionsBySlug = $permissions->keyBy('slug');

        $selectedPermissionIds = collect(old('permissions', $selectedPermissionIds ?? []))
            ->map(fn($id): int => (int) $id)
            ->all();
    @endphp

    <section class="content role-edit-page">
        <div class="pagehead role-edit-pagehead">
            <div>
                <div class="eyebrow">Access control</div>
                <h1>Edit role</h1>
                <p class="sub">Update role details, accessible records, and permitted actions.</p>
            </div>

            <div class="actions">
                <a class="btn" href="{{ route('rolesPage') }}">Back to roles</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="status">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
        @endif

        <div class="card role-form-card">
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <strong>Please correct the following errors:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('roles.update', $role) }}" method="POST" id="roleEditForm">
                @csrf
                @method('PUT')

                <div class="role-information-grid">
                    <div class="field">
                        <label for="name">Role name</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $role->name) }}"
                            maxlength="255" autocomplete="off" required>
                        @error('name')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="description">Description</label>
                        <input id="description" name="description" type="text"
                            value="{{ old('description', $role->description) }}" maxlength="1000" autocomplete="off">
                        @error('description')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <section class="role-access-section" aria-labelledby="accessScopeHeading">
                    <div class="role-access-heading">
                        <span class="role-access-kicker">Access scope</span>
                        <h2 id="accessScopeHeading">Choose accessible records</h2>
                        <p>Define which customer, supplier, and project records users with this role can access.</p>
                    </div>

                    <div class="role-access-grid">
                        @foreach ($scopeFields as $scopeField => $scopeConfig)
                            <div class="field">
                                <label for="{{ $scopeField }}">{{ $scopeConfig['label'] }}</label>

                                <select id="{{ $scopeField }}" name="{{ $scopeField }}" required>
                                    @foreach ($accessScopes as $scopeValue => $scopeName)
                                        <option value="{{ $scopeValue }}" @selected(old($scopeField, $role->{$scopeField} ?? 'assigned') === $scopeValue)>
                                            {{ $scopeName }}
                                        </option>
                                    @endforeach
                                </select>

                                <small class="role-access-help">{{ $scopeConfig['help'] }}</small>

                                @if ($errors->has($scopeField))
                                    <span class="field-error">{{ $errors->first($scopeField) }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                </section>

                <section class="permission-section" aria-labelledby="permissionHeading">
                    <div class="permission-section-heading">
                        <div>
                            <span class="permission-kicker">Permissions</span>
                            <h2 id="permissionHeading">Choose module actions</h2>
                            <p>Select individual actions or use All for every available action in a module.</p>
                        </div>

                        <div class="permission-global-actions">
                            <button class="permission-text-button" id="selectEverything" type="button">Select all</button>
                            <button class="permission-text-button permission-clear-button" id="clearEverything"
                                type="button">Clear all</button>
                        </div>
                    </div>

                    <div class="permission-list-simple">
                        @foreach ($modules as $moduleKey => $moduleLabel)
                            @php
                                $actions = $moduleActions[$moduleKey] ?? [];
                            @endphp

                            <section class="permission-module-simple" data-permission-module="{{ $moduleKey }}"
                                aria-labelledby="module-{{ $moduleKey }}">
                                <div class="permission-module-label">
                                    <span class="permission-module-dot" aria-hidden="true"></span>
                                    <span id="module-{{ $moduleKey }}">{{ $moduleLabel }}</span>
                                </div>

                                <div class="permission-options-row">
                                    <label class="permission-choice permission-choice-all">
                                        <input class="module-all-checkbox" type="checkbox"
                                            data-module="{{ $moduleKey }}"
                                            aria-label="Select all {{ $moduleLabel }} permissions">
                                        <span class="permission-choice-label">All</span>
                                        <i aria-hidden="true">✓</i>
                                    </label>

                                    @foreach ($actions as $actionKey => $actionLabel)
                                        @php
                                            $permissionSlug = $moduleKey . '.' . $actionKey;
                                            $permission = $permissionsBySlug->get($permissionSlug);
                                        @endphp

                                        @if ($permission)
                                            <label class="permission-choice">
                                                <input class="permission-checkbox" type="checkbox" name="permissions[]"
                                                    value="{{ $permission->permission_id }}"
                                                    data-module="{{ $moduleKey }}" @checked(in_array((int) $permission->permission_id, $selectedPermissionIds, true))>
                                                <span class="permission-choice-label">{{ $actionLabel }}</span>
                                                <i aria-hidden="true">✓</i>
                                            </label>
                                        @else
                                            <div class="permission-choice permission-choice-disabled" aria-disabled="true"
                                                title="{{ $permissionSlug }} is not available">
                                                <span class="permission-choice-label">{{ $actionLabel }}</span>
                                                <i aria-hidden="true"></i>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </section>
                        @endforeach
                    </div>

                    <div class="permission-selection-footer" aria-live="polite">
                        <span>Changes apply after saving.</span>
                        <strong id="permissionSelectionCount">0 selected</strong>
                    </div>

                    @error('permissions')
                        <span class="field-error">{{ $message }}</span>
                    @enderror

                    @error('permissions.*')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </section>

                <div class="form-footer role-form-footer">
                    <a class="btn" href="{{ route('rolesPage') }}">Cancel</a>
                    <button class="btn btn-primary" type="submit">Save changes</button>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .role-edit-page,
        .role-form-card {
            min-width: 0;
        }

        .role-form-card {
            width: 100%;
            overflow: hidden;
        }

        .role-form-card .alert ul {
            margin: 8px 0 0;
            padding-left: 18px;
        }

        .role-information-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .role-access-section {
            margin-top: 24px;
            padding: 20px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #fafcfb;
        }

        .role-access-heading {
            margin-bottom: 18px;
        }

        .role-access-kicker {
            display: block;
            margin-bottom: 4px;
            color: var(--green);
            font-size: 9px;
            font-weight: 850;
            letter-spacing: .11em;
            text-transform: uppercase;
        }

        .role-access-heading h2 {
            margin: 0;
            color: var(--ink);
            font-size: 16px;
        }

        .role-access-heading p {
            max-width: 680px;
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 10px;
            line-height: 1.5;
        }

        .role-access-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .role-access-grid select {
            min-height: 44px;
            background: var(--white);
        }

        .role-access-help {
            color: var(--muted);
            font-size: 9px;
            line-height: 1.5;
        }

        .role-access-legend {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid var(--line);
        }

        .role-access-legend>div {
            min-width: 0;
            padding: 10px 12px;
            border: 1px solid #e2e9e5;
            border-radius: 9px;
            background: var(--white);
        }

        .role-access-legend strong {
            display: block;
            margin-bottom: 4px;
            color: var(--green);
            font-size: 10px;
        }

        .role-access-legend span {
            display: block;
            color: var(--muted);
            font-size: 9px;
            line-height: 1.5;
        }

        .permission-section {
            margin-top: 24px;
            padding-top: 22px;
            border-top: 1px solid var(--line);
        }

        .permission-section-heading {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 18px;
        }

        .permission-kicker {
            display: block;
            margin-bottom: 4px;
            color: var(--green);
            font-size: 9px;
            font-weight: 850;
            letter-spacing: .11em;
            text-transform: uppercase;
        }

        .permission-section-heading h2 {
            margin: 0;
            color: var(--ink);
            font-size: 16px;
            font-weight: 850;
        }

        .permission-section-heading p {
            max-width: 620px;
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 10px;
            line-height: 1.55;
        }

        .permission-global-actions {
            display: flex;
            flex-shrink: 0;
            gap: 8px;
        }

        .permission-text-button {
            min-height: 38px;
            padding: 7px 13px;
            border: 1px solid var(--green2);
            border-radius: 8px;
            background: var(--mint);
            color: var(--green);
            font-size: 10px;
            font-weight: 800;
            cursor: pointer;
        }

        .permission-clear-button {
            border-color: var(--line);
            background: var(--white);
            color: var(--muted);
        }

        .permission-list-simple {
            display: grid;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: var(--white);
        }

        .permission-module-simple {
            display: grid;
            grid-template-columns: minmax(155px, .85fr) minmax(0, 3.15fr);
            align-items: stretch;
            min-width: 0;
            border-bottom: 1px solid var(--line);
        }

        .permission-module-simple:last-child {
            border-bottom: 0;
        }

        .permission-module-simple.has-selection {
            background: #fbfdfb;
        }

        .permission-module-label {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
            padding: 15px 16px;
            border-right: 1px solid var(--line);
            background: #f7f9f7;
            color: var(--ink);
            font-size: 11px;
            font-weight: 850;
        }

        .permission-module-dot {
            display: block;
            flex: 0 0 8px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #c8d2cc;
        }

        .permission-module-simple.has-selection .permission-module-dot {
            background: var(--green);
            box-shadow: 0 0 0 4px rgba(36, 88, 73, .09);
        }

        .permission-options-row {
            display: grid;
            grid-template-columns: repeat(5, minmax(84px, 1fr));
            align-items: center;
            gap: 8px;
            min-width: 0;
            padding: 10px 12px;
        }

        .permission-choice {
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
            min-height: 42px;
            padding: 8px 9px;
            border: 1px solid transparent;
            border-radius: 8px;
            color: var(--ink);
            cursor: pointer;
        }

        .permission-choice:hover {
            border-color: var(--line);
            background: #f6f9f7;
        }

        .permission-choice-all {
            background: #f8faf8;
        }

        .permission-choice-label {
            order: 2;
            min-width: 0;
            overflow: hidden;
            font-size: 9px;
            font-weight: 800;
            line-height: 1.25;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .permission-choice input {
            position: absolute;
            width: 1px;
            height: 1px;
            margin: 0;
            opacity: 0;
        }

        .permission-choice i {
            order: 1;
            display: grid;
            flex: 0 0 21px;
            width: 21px;
            height: 21px;
            border: 1px solid #b8c5bd;
            border-radius: 5px;
            background: var(--white);
            color: transparent;
            font-style: normal;
            font-size: 11px;
            font-weight: 900;
            place-items: center;
        }

        .permission-choice:has(input:checked) {
            border-color: #b7d0c2;
            background: var(--mint);
        }

        .permission-choice:has(input:checked) i {
            border-color: var(--green);
            background: var(--green);
            color: var(--white);
        }

        .permission-choice:has(input:indeterminate) {
            border-color: #dec784;
            background: #fff9e8;
        }

        .permission-choice:has(input:indeterminate) i {
            border-color: #b58a24;
            background: #f5d77d;
            color: transparent;
            font-size: 0;
        }

        .permission-choice:has(input:indeterminate) i::before {
            color: #674b06;
            content: '−';
            font-size: 14px;
        }

        .permission-choice input:focus-visible~i {
            outline: 3px solid rgba(36, 88, 73, .18);
            outline-offset: 2px;
        }

        .permission-choice-disabled {
            color: #a4ada8;
            cursor: not-allowed;
            opacity: .55;
        }

        .permission-choice-disabled:hover {
            border-color: transparent;
            background: transparent;
        }

        .permission-selection-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-top: 12px;
            color: var(--muted);
            font-size: 9px;
        }

        .permission-selection-footer strong {
            color: var(--green);
            font-size: 10px;
            font-weight: 850;
        }

        @media (max-width: 1050px) {
            .role-access-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .role-access-grid .field:last-child {
                grid-column: 1 / -1;
            }

            .role-access-legend {
                grid-template-columns: 1fr;
            }

            .permission-module-simple {
                grid-template-columns: minmax(145px, .7fr) minmax(0, 3.3fr);
            }

            .permission-options-row {
                grid-template-columns: repeat(3, minmax(90px, 1fr));
            }
        }

        @media (max-width: 720px) {
            .role-edit-page {
                padding-right: 14px;
                padding-left: 14px;
            }

            .role-edit-pagehead {
                align-items: stretch;
                flex-direction: column;
            }

            .role-edit-pagehead .actions,
            .role-edit-pagehead .btn {
                width: 100%;
            }

            .role-form-card {
                padding: 16px;
            }

            .role-information-grid,
            .role-access-grid {
                grid-template-columns: 1fr;
            }

            .role-access-grid .field:last-child {
                grid-column: auto;
            }

            .role-access-section {
                padding: 15px;
            }

            .permission-section-heading {
                align-items: stretch;
                flex-direction: column;
            }

            .permission-global-actions {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                width: 100%;
            }

            .permission-text-button {
                width: 100%;
            }

            .permission-module-simple {
                grid-template-columns: 1fr;
            }

            .permission-module-label {
                min-height: 46px;
                padding: 12px 13px;
                border-right: 0;
                border-bottom: 1px solid var(--line);
            }

            .permission-options-row {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                padding: 10px;
            }

            .permission-choice-label {
                overflow: visible;
                text-overflow: clip;
                white-space: normal;
            }

            .role-form-footer {
                align-items: stretch;
                flex-direction: column-reverse;
            }

            .role-form-footer .btn {
                width: 100%;
            }
        }

        @media (max-width: 390px) {
            .role-form-card {
                padding: 13px;
            }

            .permission-global-actions,
            .permission-options-row {
                grid-template-columns: 1fr;
            }

            .permission-selection-footer {
                align-items: flex-start;
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('roleEditForm');
            if (!form) return;

            const items = Array.from(form.querySelectorAll('.permission-checkbox'));
            const allItems = Array.from(form.querySelectorAll('.module-all-checkbox'));
            const count = form.querySelector('#permissionSelectionCount');
            const group = moduleName => items.filter(item => item.dataset.module === moduleName);

            function refresh() {
                allItems.forEach(allItem => {
                    const moduleItems = group(allItem.dataset.module);
                    const selectedCount = moduleItems.filter(item => item.checked).length;
                    allItem.checked = moduleItems.length > 0 && selectedCount === moduleItems.length;
                    allItem.indeterminate = selectedCount > 0 && selectedCount < moduleItems.length;
                    allItem.closest('.permission-module-simple')?.classList.toggle('has-selection',
                        selectedCount > 0);
                });

                if (count) {
                    count.textContent = items.filter(item => item.checked).length + ' selected';
                }
            }

            allItems.forEach(allItem => {
                allItem.addEventListener('change', function() {
                    group(allItem.dataset.module).forEach(item => item.checked = allItem.checked);
                    refresh();
                });
            });

            items.forEach(item => item.addEventListener('change', refresh));
            form.querySelector('#selectEverything')?.addEventListener('click', function() {
                items.forEach(item => item.checked = true);
                refresh();
            });
            form.querySelector('#clearEverything')?.addEventListener('click', function() {
                items.forEach(item => item.checked = false);
                refresh();
            });

            refresh();
        });
    </script>
@endpush
