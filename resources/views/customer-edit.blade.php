@extends('layouts.master')

@section('title', 'Edit Customer | Golden Pride')
@section('page-name', 'Customer / Edit')

@section('content')
    <section class="content">
        <div class="pagehead">
            <div>
                <div class="eyebrow">Directory</div>
                <h1>Edit Customer</h1>
                <p class="sub">Update customer information and contact persons.</p>
            </div>
            <div class="actions">
                <a class="btn" href="{{ route('customersPage') }}">&larr; Back</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="status">{{ session('success') }}</div>
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

        <div class="card">
            <form action="{{ route('customers.update', $customer) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <div class="field">
                        <label for="name">Customer name</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $customer->name) }}" maxlength="255" autofocus required>
                        @error('name')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="field">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <option value="Active" @selected(old('status', $customer->status) === 'Active')>Active</option>
                            <option value="Inactive" @selected(old('status', $customer->status) === 'Inactive')>Inactive</option>
                        </select>
                        @error('status')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="field full">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" rows="3" maxlength="1000">{{ old('address', $customer->address) }}</textarea>
                        @error('address')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    @include('customer-contacts-fields')

                    <div class="field full">
                        <label for="notes">Notes</label>
                        <textarea id="notes" name="notes" rows="4" maxlength="2000">{{ old('notes', $customer->notes) }}</textarea>
                        @error('notes')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-footer">
                    <a class="btn" href="{{ route('customersPage') }}">Cancel</a>
                    <button class="btn btn-primary" type="submit">Save changes</button>
                </div>
            </form>
        </div>
    </section>
@endsection
