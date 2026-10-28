@extends('layouts.master')

@section('title', 'Edit Project | Golden Pride')
@section('page-name', 'Projects / Edit')

@section('content')
    <section class="content project-entry-page project-edit-page">
        <div class="project-entry-card">
            <div class="project-entry-accent" aria-hidden="true"></div>
            <div class="project-entry-header">
                <div>
                    <h1>Edit Project</h1>
                    <p>Keep customer, supplier, commercial, and schedule information up to date.</p>
                </div>

                <a href="{{ route('projectsPage') }}" class="project-entry-close" aria-label="Close and return to project">
                    &times;
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger project-entry-alert">
                    <strong>Please correct the following fields:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('projects.update', $project) }}" method="POST" class="project-entry-form">
                @csrf
                @method('PUT')

                <div class="project-entry-grid">
                    <div class="project-entry-field project-entry-project">
                        <label for="name">Project</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $project->name) }}"
                            maxlength="255" required>
                        @error('name')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="project-entry-field">
                        <label for="customer_id">End User</label>
                        <select id="customer_id" name="customer_id" required>
                            <option value="">-- Select End User --</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->customer_id }}" @selected((string) old('customer_id', $project->customer_id) === (string) $customer->customer_id)>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="project-entry-field">
                        <label for="supplier_id">Supplier</label>
                        <select id="supplier_id" name="supplier_id" required>
                            <option value="">-- Select Supplier --</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->supplier_id }}" @selected((string) old('supplier_id', $project->supplier_id) === (string) $supplier->supplier_id)>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="project-entry-field">
                        <label for="vendor">Vendor</label>
                        <input id="vendor" name="vendor" type="text" value="{{ old('vendor', $project->vendor) }}"
                            maxlength="255" required>
                        @error('vendor')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="project-entry-field">
                        <label for="accountManagerName">Account Manager (AM)</label>
                        <input id="accountManagerName" name="accountManagerName" type="text"
                            value="{{ old('accountManagerName', $project->accountManagerName) }}" maxlength="255" required>
                        @error('accountManagerName')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="project-entry-field">
                        <label for="productCategory">Product Category</label>
                        <select id="productCategory" name="productCategory" required>
                            <option value="">-- Select Product Category --</option>
                            @foreach ($productCategories as $category)
                                <option value="{{ $category }}" @selected(old('productCategory', $project->productCategory) === $category)>{{ $category }}
                                </option>
                            @endforeach
                        </select>
                        @error('productCategory')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="project-entry-field">
                        <label for="type">Project Type</label>
                        <select id="type" name="type" required>
                            @foreach ($projectTypes as $projectType)
                                <option value="{{ $projectType }}" @selected(old('type', $project->type) === $projectType)>{{ $projectType }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="project-entry-field">
                        <label for="status">Progress Status</label>
                        <select id="status" name="status" required>
                            @foreach ($progressStatuses as $progressStatus)
                                <option value="{{ $progressStatus }}" @selected(old('status', $project->status) === $progressStatus)>{{ $progressStatus }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                <br>

                <div class="project-entry-grid project-entry-grid-schedule">
                    <div class="project-entry-field">
                        <label for="date">Start Date (Created Date)</label>
                        <input id="date" name="date" type="date"
                            value="{{ old('date', $project->date?->format('Y-m-d')) }}" required>
                        @error('date')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="project-entry-field">
                        <label for="end_date">End Date</label>
                        <input id="end_date" name="end_date" type="date"
                            value="{{ old('end_date', $project->end_date?->format('Y-m-d')) }}">
                        @error('end_date')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="project-entry-field project-entry-remark">
                        <label for="remark">Remark</label>
                        <textarea id="remark" name="remark" rows="4" maxlength="2000">{{ old('remark', $project->remark) }}</textarea>
                        @error('remark')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="project-entry-footer">
                    <a class="project-entry-cancel" href="{{ route('projectsPage') }}">Cancel</a>
                    <button class="project-entry-save" type="submit">Save Changes</button>
                </div>
            </form>

            @php
                $activeCostTotal = $project->costItems->where('is_active', true)->sum('amount');
                $activeRevenueTotal = $project->revenueItems->where('is_active', true)->sum('amount');
                $grossProfit = $activeRevenueTotal - $activeCostTotal;
                $grossProfitPercent = $activeRevenueTotal > 0 ? ($grossProfit / $activeRevenueTotal) * 100 : 0;
            @endphp
            <br><br>
            <div class="financial-overview">
                <div class="financial-overview-card">
                    <span>Cost</span>
                    <strong>{{ number_format($activeCostTotal, 2) }} MMK</strong>
                </div>
                <div class="financial-overview-card">
                    <span>Revenue</span>
                    <strong>{{ number_format($activeRevenueTotal, 2) }} MMK</strong>
                </div>
                <div class="financial-overview-card">
                    <span>GP</span>
                    <strong class="{{ $grossProfit < 0 ? 'negative' : '' }}">
                        {{ number_format($grossProfit, 2) }} MMK
                    </strong>
                </div>
                <div class="financial-overview-card">
                    <span>GP %</span>
                    <strong class="{{ $grossProfitPercent < 0 ? 'negative' : '' }}">
                        {{ number_format($grossProfitPercent, 2) }}%
                    </strong>
                </div>
            </div>

            <section class="financial-section">
                <div class="financial-section-heading">
                    <div>
                        <h2>Cost</h2>
                        <p>Project cost line items. Deactivated records are excluded from totals.</p>
                    </div>
                </div>

                <form class="financial-entry-form" action="{{ route('projects.costs.store', $project) }}"
                    method="POST">
                    @csrf
                    <input type="date" name="item_date" value="{{ old('item_date', now()->format('Y-m-d')) }}"
                        required>
                    <input type="text" name="description" value="{{ old('description') }}"
                        placeholder="Cost description" maxlength="255" required>
                    <input type="number" name="amount" value="{{ old('amount') }}" min="0.01" step="0.01"
                        placeholder="Amount" required>
                    <button type="submit">Add Cost</button>
                    <button type="reset" class="financial-clear-button">Clear</button>
                </form>

                <div class="financial-table-wrap">
                    <table class="financial-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th class="financial-number">Amount</th>
                                <th class="financial-actions-heading">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($project->costItems as $costItem)
                                <tr class="{{ $costItem->is_active ? '' : 'financial-row-inactive' }}">
                                    <td>{{ $costItem->item_date->format('d M Y') }}</td>
                                    <td>{{ $costItem->description }}</td>
                                    <td class="financial-number">{{ number_format((float) $costItem->amount, 2) }} MMK
                                    </td>
                                    <td>
                                        <div class="financial-row-actions">
                                            <button type="button"
                                                data-financial-edit="cost-{{ $costItem->project_cost_id }}">Edit</button>
                                            <form action="{{ route('projects.costs.destroy', [$project, $costItem]) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit">
                                                    Remove
                                                </button>
                                            </form>
                                        </div>
                                        <form id="cost-{{ $costItem->project_cost_id }}" class="financial-inline-edit"
                                            action="{{ route('projects.costs.update', [$project, $costItem]) }}"
                                            method="POST" hidden>
                                            @csrf
                                            @method('PUT')
                                            <input type="date" name="item_date"
                                                value="{{ $costItem->item_date->format('Y-m-d') }}" required>
                                            <input type="text" name="description"
                                                value="{{ $costItem->description }}" maxlength="255" required>
                                            <input type="number" name="amount" value="{{ $costItem->amount }}"
                                                min="0.01" step="0.01" required>
                                            <button type="submit">Save</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="financial-empty">No cost items yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="financial-section">
                <div class="financial-section-heading">
                    <div>
                        <h2>Revenue</h2>
                        <p>Project revenue line items. Deactivated records are excluded from totals.</p>
                    </div>
                </div>

                <form class="financial-entry-form" action="{{ route('projects.revenues.store', $project) }}"
                    method="POST">
                    @csrf
                    <input type="date" name="item_date" value="{{ old('item_date', now()->format('Y-m-d')) }}"
                        required>
                    <input type="text" name="description" value="{{ old('description') }}"
                        placeholder="Revenue description" maxlength="255" required>
                    <input type="number" name="amount" value="{{ old('amount') }}" min="0.01" step="0.01"
                        placeholder="Amount" required>
                    <button type="submit">Add Revenue</button>
                    <button type="reset" class="financial-clear-button">Clear</button>
                </form>

                <div class="financial-table-wrap">
                    <table class="financial-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th class="financial-number">Amount</th>
                                <th class="financial-actions-heading">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($project->revenueItems as $revenueItem)
                                <tr class="{{ $revenueItem->is_active ? '' : 'financial-row-inactive' }}">
                                    <td>{{ $revenueItem->item_date->format('d M Y') }}</td>
                                    <td>{{ $revenueItem->description }}</td>
                                    <td class="financial-number">{{ number_format((float) $revenueItem->amount, 2) }} MMK
                                    </td>
                                    <td>
                                        <div class="financial-row-actions">
                                            <button type="button"
                                                data-financial-edit="revenue-{{ $revenueItem->project_revenue_id }}">Edit</button>
                                            <form
                                                action="{{ route('projects.revenues.destroy', [$project, $revenueItem]) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit">
                                                    Remove
                                                </button>
                                            </form>
                                        </div>
                                        <form id="revenue-{{ $revenueItem->project_revenue_id }}"
                                            class="financial-inline-edit"
                                            action="{{ route('projects.revenues.update', [$project, $revenueItem]) }}"
                                            method="POST" hidden>
                                            @csrf
                                            @method('PUT')
                                            <input type="date" name="item_date"
                                                value="{{ $revenueItem->item_date->format('Y-m-d') }}" required>
                                            <input type="text" name="description"
                                                value="{{ $revenueItem->description }}" maxlength="255" required>
                                            <input type="number" name="amount" value="{{ $revenueItem->amount }}"
                                                min="0.01" step="0.01" required>
                                            <button type="submit">Save</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="financial-empty">No revenue items yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startDate = document.getElementById('date');
            const endDate = document.getElementById('end_date');

            function updateEndDateMinimum() {
                if (!startDate || !endDate) return;
                endDate.min = startDate.value;
                if (endDate.value && endDate.value < startDate.value) endDate.value = '';
            }

            startDate?.addEventListener('change', updateEndDateMinimum);

            updateEndDateMinimum();

            document.querySelectorAll('[data-financial-edit]').forEach(function(button) {
                button.addEventListener('click', function() {
                    const form = document.getElementById(button.dataset.financialEdit);
                    if (form) {
                        form.hidden = !form.hidden;
                    }
                });
            });
        });
    </script>
@endpush
