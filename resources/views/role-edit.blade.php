@extends('layouts.master')

@section('title', 'Edit role | Golden Pride')
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

        /*
         * Projects intentionally do not display Deactivate.
         * The other modules retain View, Create, Edit, and Deactivate.
         */
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

        $permissionsBySlug = $permissions->keyBy('slug');

        $selectedPermissionIds = collect(
            old('permissions', $selectedPermissionIds ?? [])
        )
            ->map(fn ($id): int => (int) $id)
            ->all();
    @endphp

    <section class="content">
        <div class="pagehead">
            <div>
                <div class="eyebrow">Access control</div>
                <h1>Edit role</h1>
                <p class="sub">Define role details and permitted actions.</p>
            </div>

            <div class="actions">
                <a class="btn" href="{{ route('rolesPage') }}">
                    Back to roles
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

        <div class="card role-form-card">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Please correct the following errors:</strong>

                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                action="{{ route('roles.update', $role) }}"
                method="POST"
                id="roleEditForm"
            >
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <div class="field">
                        <label for="name">Role name</label>

                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name', $role->name) }}"
                            maxlength="255"
                            required
                        >

                        @error('name')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="description">Description</label>

                        <input
                            id="description"
                            name="description"
                            type="text"
                            value="{{ old('description', $role->description) }}"
                            maxlength="1000"
                        >

                        @error('description')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field full permission-section">
                        <div class="permission-section-heading">
                            <div>
                                <span class="permission-kicker">Permissions</span>
                                <h2>Choose module access</h2>
                                <p>
                                    Select individual actions or use All to select
                                    every available action in a module.
                                </p>
                            </div>

                            <div class="permission-global-actions">
                                <button
                                    class="permission-text-button"
                                    id="selectEverything"
                                    type="button"
                                >
                                    Select all
                                </button>

                                <button
                                    class="permission-text-button permission-clear-button"
                                    id="clearEverything"
                                    type="button"
                                >
                                    Clear all
                                </button>
                            </div>
                        </div>

                        <div class="permission-list-simple">
                            @foreach ($modules as $moduleKey => $moduleLabel)
                                @php
                                    $actions = $moduleActions[$moduleKey] ?? [];
                                @endphp

                                <section
                                    class="permission-module-simple"
                                    data-permission-module="{{ $moduleKey }}"
                                >
                                    <div class="permission-module-label">
                                        <span
                                            class="permission-module-dot"
                                            aria-hidden="true"
                                        ></span>
                                        <span>{{ $moduleLabel }}</span>
                                    </div>

                                    <div class="permission-options-row">
                                        <label class="permission-choice permission-choice-all">
                                            <input
                                                class="module-all-checkbox"
                                                type="checkbox"
                                                data-module="{{ $moduleKey }}"
                                            >
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
                                                    <input
                                                        class="permission-checkbox"
                                                        type="checkbox"
                                                        name="permissions[]"
                                                        value="{{ $permission->permission_id }}"
                                                        data-module="{{ $moduleKey }}"
                                                        @checked(
                                                            in_array(
                                                                (int) $permission->permission_id,
                                                                $selectedPermissionIds,
                                                                true
                                                            )
                                                        )
                                                    >
                                                    <span class="permission-choice-label">
                                                        {{ $actionLabel }}
                                                    </span>
                                                    <i aria-hidden="true">✓</i>
                                                </label>
                                            @else
                                                <div
                                                    class="permission-choice permission-choice-disabled"
                                                    aria-disabled="true"
                                                    title="{{ $permissionSlug }} is not available"
                                                >
                                                    <span class="permission-choice-label">
                                                        {{ $actionLabel }}
                                                    </span>
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
                    </div>
                </div>

                <div class="form-footer">
                    <a class="btn" href="{{ route('rolesPage') }}">
                        Cancel
                    </a>

                    <button class="btn btn-primary" type="submit">
                        Save changes
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const permissionItems = Array.from(
                document.querySelectorAll('.permission-checkbox')
            );

            const moduleAllItems = Array.from(
                document.querySelectorAll('.module-all-checkbox')
            );

            const selectionCount = document.getElementById(
                'permissionSelectionCount'
            );

            const selectEverythingButton = document.getElementById(
                'selectEverything'
            );

            const clearEverythingButton = document.getElementById(
                'clearEverything'
            );

            function getModuleItems(moduleName) {
                return permissionItems.filter(function (item) {
                    return item.dataset.module === moduleName;
                });
            }

            function updateSelectionState() {
                moduleAllItems.forEach(function (moduleAllItem) {
                    const moduleItems = getModuleItems(
                        moduleAllItem.dataset.module
                    );

                    const selectedItems = moduleItems.filter(function (item) {
                        return item.checked;
                    });

                    moduleAllItem.checked =
                        moduleItems.length > 0 &&
                        selectedItems.length === moduleItems.length;

                    moduleAllItem.indeterminate =
                        selectedItems.length > 0 &&
                        selectedItems.length < moduleItems.length;

                    const moduleContainer = moduleAllItem.closest(
                        '.permission-module-simple'
                    );

                    moduleContainer?.classList.toggle(
                        'has-selection',
                        selectedItems.length > 0
                    );
                });

                const totalSelected = permissionItems.filter(function (item) {
                    return item.checked;
                }).length;

                if (selectionCount) {
                    selectionCount.textContent = totalSelected + ' selected';
                }
            }

            moduleAllItems.forEach(function (moduleAllItem) {
                moduleAllItem.addEventListener('change', function () {
                    getModuleItems(moduleAllItem.dataset.module).forEach(
                        function (item) {
                            item.checked = moduleAllItem.checked;
                        }
                    );

                    updateSelectionState();
                });
            });

            permissionItems.forEach(function (item) {
                item.addEventListener('change', updateSelectionState);
            });

            selectEverythingButton?.addEventListener('click', function () {
                permissionItems.forEach(function (item) {
                    item.checked = true;
                });

                updateSelectionState();
            });

            clearEverythingButton?.addEventListener('click', function () {
                permissionItems.forEach(function (item) {
                    item.checked = false;
                });

                updateSelectionState();
            });

            updateSelectionState();
        });
    </script>
@endpush
