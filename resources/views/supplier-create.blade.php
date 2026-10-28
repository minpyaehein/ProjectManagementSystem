@extends('layouts.master')
@section('title', 'Create Supplier | Golden Pride')
@section('page-name', 'Supplier / Create')
@section('content')
<section class="content">
    <div class="pagehead">
        <div><div class="eyebrow">Directory</div><h1>Create Supplier</h1><p class="sub">Add supplier information and one or more contact persons.</p></div>
        <div class="actions"><a class="btn" href="{{ route('suppliersPage') }}">&larr; Back</a></div>
    </div>
    @if ($errors->any())
        <div class="alert alert-danger"><strong>Please correct the following fields:</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <div class="card">
        <form action="{{ route('suppliers.store') }}" method="POST">
            @csrf
            <div class="form-grid">
                <div class="field"><label for="name">Supplier name</label><input id="name" name="name" type="text" value="{{ old('name') }}" maxlength="255" autofocus required>@error('name')<span class="field-error">{{ $message }}</span>@enderror</div>
                <div class="field"><label for="category">Category</label><select id="category" name="category" required><option value="">Select category</option>@foreach ($categories as $category)<option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>@endforeach</select>@error('category')<span class="field-error">{{ $message }}</span>@enderror</div>
                <div class="field"><label for="status">Status</label><select id="status" name="status" required><option value="Active" @selected(old('status', 'Active') === 'Active')>Active</option><option value="Inactive" @selected(old('status') === 'Inactive')>Inactive</option></select>@error('status')<span class="field-error">{{ $message }}</span>@enderror</div>
                <div class="field full"><label for="address">Address</label><textarea id="address" name="address" rows="3" maxlength="1000">{{ old('address') }}</textarea>@error('address')<span class="field-error">{{ $message }}</span>@enderror</div>
                @include('supplier-contacts-fields')
                <div class="field full"><label for="notes">Notes</label><textarea id="notes" name="notes" rows="4" maxlength="2000">{{ old('notes') }}</textarea>@error('notes')<span class="field-error">{{ $message }}</span>@enderror</div>
            </div>
            <div class="form-footer"><a class="btn" href="{{ route('suppliersPage') }}">Cancel</a><button class="btn btn-primary" type="submit">Save record</button></div>
        </form>
    </div>
</section>
@endsection
