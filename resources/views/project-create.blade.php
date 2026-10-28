@extends('layouts.master')

@section('title', 'Create Project | Golden Pride')
@section('page-name', 'Projects / Create')

@section('content')
    <section class="content project-entry-page">
        <div class="project-entry-card">
            <div class="project-entry-accent" aria-hidden="true"></div>
            <div class="project-entry-header">
                <div>
                    <h1>New Project</h1>
                    <p>Revenue and Cost with automatic financial summary.</p>
                </div>

                <a href="{{ route('projectsPage') }}" class="project-entry-close" aria-label="Close and return to projects">
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

            <form action="{{ route('projects.store') }}" method="POST" class="project-entry-form">
                @csrf

                <div class="project-entry-grid">
                    <div class="project-entry-field project-entry-project">
                        <label for="name">Project</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" maxlength="255"
                            autofocus required>
                        @error('name')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="project-entry-field">
                        <label for="customer_id">End User</label>
                        <select id="customer_id" name="customer_id" required>
                            <option value="">-- Select End User --</option>
                            @forelse ($customers as $customer)
                                <option value="{{ $customer->customer_id }}" @selected((string) old('customer_id') === (string) $customer->customer_id)>
                                    {{ $customer->name }}
                                </option>
                            @empty
                                <option value="" disabled>No active end users available</option>
                            @endforelse
                        </select>
                        @error('customer_id')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="project-entry-field">
                        <label for="supplier_id">Supplier</label>
                        <select id="supplier_id" name="supplier_id" required>
                            <option value="">-- Select Supplier --</option>
                            @forelse ($suppliers as $supplier)
                                <option value="{{ $supplier->supplier_id }}" @selected((string) old('supplier_id') === (string) $supplier->supplier_id)>
                                    {{ $supplier->name }}
                                </option>
                            @empty
                                <option value="" disabled>No active suppliers available</option>
                            @endforelse
                        </select>
                        @error('supplier_id')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="project-entry-field">
                        <label for="vendor">Vendor</label>
                        <input id="vendor" name="vendor" type="text" value="{{ old('vendor') }}"
                            placeholder="Vendor / Brand name" maxlength="255" required>
                        @error('vendor')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="project-entry-field">
                        <label for="accountManagerName">Account Manager (AM)</label>
                        <input id="accountManagerName" name="accountManagerName" type="text"
                            value="{{ old('accountManagerName') }}" placeholder="Account Manager name" maxlength="255"
                            required>
                        @error('accountManagerName')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="project-entry-field">
                        <label for="productCategory">Product Category</label>
                        <select id="productCategory" name="productCategory" required>
                            <option value="">-- Select Product Category --</option>
                            @foreach ($productCategories as $category)
                                <option value="{{ $category }}" @selected(old('productCategory') === $category)>
                                    {{ $category }}
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
                                <option value="{{ $projectType }}" @selected(old('type', 'New Project') === $projectType)>
                                    {{ $projectType }}
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
                                <option value="{{ $progressStatus }}" @selected(old('status', 'Prospecting') === $progressStatus)>
                                    {{ $progressStatus }}
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
                            value="{{ old('date', now()->format('Y-m-d')) }}" required>
                        @error('date')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="project-entry-field">
                        <label for="end_date">End Date</label>
                        <input id="end_date" name="end_date" type="date" value="{{ old('end_date') }}"
                            min="{{ old('date', now()->format('Y-m-d')) }}">
                        @error('end_date')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="project-entry-field project-entry-remark">
                        <label for="remark">Remark</label>
                        <textarea id="remark" name="remark" rows="4" maxlength="2000">{{ old('remark') }}</textarea>
                        @error('remark')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="project-entry-footer">
                    <a class="project-entry-cancel" href="{{ route('projectsPage') }}">Cancel</a>
                    <button class="project-entry-save" type="submit">
                        <span>Save Project</span>
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startDate = document.getElementById('date');
            const endDate = document.getElementById('end_date');

            function updateEndDateMinimum() {
                if (!startDate || !endDate) {
                    return;
                }

                endDate.min = startDate.value;

                if (endDate.value && endDate.value < startDate.value) {
                    endDate.value = '';
                }
            }

            startDate?.addEventListener('change', updateEndDateMinimum);
            updateEndDateMinimum();
        });
    </script>
@endpush
