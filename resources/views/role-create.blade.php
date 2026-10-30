@extends('layouts.master')

@section('title', 'Create role | Golden Pride')
@section('page-name', 'Roles / Create')

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

        $selectedPermissionIds = collect(old('permissions', []))->map(fn($id): int => (int) $id)->all();
    @endphp

    <section class="content role-create-page">
        <div class="pagehead">
            <div>
                <div class="eyebrow">Access control</div>
                <h1>Create role</h1>
                <p class="sub">Define role details, accessible records, and permitted actions.</p>
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

            <form action="{{ route('roles.store') }}" method="POST" id="roleCreateForm">
                @csrf

                <div class="form-grid">
                    <div class="field">
                        <label for="name">Role name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" maxlength="255"
                            autocomplete="off" required>
                        @error('name')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="description">Description</label>
                        <input id="description" name="description" type="text" value="{{ old('description') }}"
                            maxlength="1000" autocomplete="off">
                        @error('description')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <section class="field full role-access-section" aria-labelledby="accessScopeHeading">
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
                                            <option value="{{ $scopeValue }}" @selected(old($scopeField, 'assigned') === $scopeValue)>
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

                    <section class="field full permission-section" aria-labelledby="permissionHeading">
                        <div class="permission-section-heading">
                            <div>
                                <span class="permission-kicker">Permissions</span>
                                <h2 id="permissionHeading">Choose module actions</h2>
                                <p>Select individual actions or use All for every available action in a module.</p>
                            </div>

                            <div class="permission-global-actions">
                                <button class="permission-text-button" id="selectEverything" type="button">Select
                                    all</button>
                                <button class="permission-text-button permission-clear-button" id="clearEverything"
                                    type="button">Clear all</button>
                            </div>
                        </div>

                        <div class="permission-list-simple">
                            @foreach ($modules as $moduleKey => $moduleLabel)
                                @php
                                    $actions = $moduleActions[$moduleKey] ?? [];
                                @endphp

                                <section class="permission-module-simple" data-permission-module="{{ $moduleKey }}">
                                    <div class="permission-module-label">
                                        <span class="permission-module-dot" aria-hidden="true"></span>
                                        <span>{{ $moduleLabel }}</span>
                                    </div>

                                    <div class="permission-options-row">
                                        <label class="permission-choice permission-choice-all">
                                            <input class="module-all-checkbox" type="checkbox"
                                                data-module="{{ $moduleKey }}">
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
                                                <div class="permission-choice permission-choice-disabled"
                                                    aria-disabled="true" title="{{ $permissionSlug }} is not available">
                                                    <span class="permission-choice-label">{{ $actionLabel }}</span>
                                                    <i aria-hidden="true"></i>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </section>
                            @endforeach
                        </div>

                        <div class="permission-selection-footer">
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
                </div>

                <div class="form-footer">
                    <a class="btn" href="{{ route('rolesPage') }}">Cancel</a>
                    <button class="btn btn-primary" type="submit">Save role</button>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .role-create-page,
        .role-form-card {
            min-width: 0;
        }

        .role-access-section {
            margin-top: 4px;
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

        @media (max-width: 900px) {
            .role-access-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .role-access-grid .field:last-child {
                grid-column: 1 / -1;
            }

            .role-access-legend {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 650px) {

            .role-form-card,
            .role-access-section {
                padding: 15px;
            }

            .role-access-grid {
                grid-template-columns: 1fr;
            }

            .role-access-grid .field:last-child {
                grid-column: auto;
            }

            .permission-section-heading {
                align-items: stretch;
                flex-direction: column;
            }

            .permission-global-actions {
                display: grid;
                grid-template-columns: 1fr 1fr;
                width: 100%;
            }

            .permission-text-button {
                width: 100%;
            }

            .permission-list-simple {
                display: grid;
                gap: 12px;
                overflow: visible;
                border: 0;
                background: transparent;
            }

            .permission-module-simple {
                display: block;
                min-width: 0;
                overflow: hidden;
                border: 1px solid var(--line);
                border-radius: 10px;
                background: var(--white);
            }

            .permission-module-label {
                border-right: 0;
                border-bottom: 1px solid var(--line);
            }

            .permission-options-row {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .form-footer {
                align-items: stretch;
                flex-direction: column-reverse;
            }

            .form-footer .btn,
            .form-footer button {
                width: 100%;
            }
        }

        @media (max-width: 390px) {

            .permission-global-actions,
            .permission-options-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('roleCreateForm');
            if (!form) return;

            const items = Array.from(form.querySelectorAll('.permission-checkbox'));
            const allItems = Array.from(form.querySelectorAll('.module-all-checkbox'));
            const count = document.getElementById('permissionSelectionCount');
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
            document.getElementById('selectEverything')?.addEventListener('click', function() {
                items.forEach(item => item.checked = true);
                refresh();
            });
            document.getElementById('clearEverything')?.addEventListener('click', function() {
                items.forEach(item => item.checked = false);
                refresh();
            });

            refresh();
        });
    </script>
@endpush
