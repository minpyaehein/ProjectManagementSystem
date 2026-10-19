@extends('admin.layouts.master')

@section('content')
    <div class="container-fluid">

        <div class="">
            <div class="row">
                <div class="col-4">
                    <div class="card">
                        <div class="card-body shadow">
                            <form action="{{ route('paymentUpdate') }}" method="post">
                        @csrf

                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label class="form-label">Payment Type</label>
                                        <input type="text" name="paymentType" value="{{ old('paymentType', $data->type) }}"
                                            class="form-control @error('paymentType') is-invalid @enderror"
                                            placeholder="Enter Payment Type...">
                                        @error('paymentType')
                                            <small class=" invalid-feedback">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label class="form-label">Account Number</label>
                                        <input type="text" name="accountNumber" value="{{ old('accountNumber', $data->account_number) }}"
                                            class="form-control @error('accountNumber') is-invalid @enderror"
                                            placeholder="Enter Account Number...">
                                        @error('accountNumber')
                                            <small class=" invalid-feedback">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label class="form-label">Account Name</label>
                                        <input type="text" name="accountName" value="{{ old('accountName', $data->account_name) }}"
                                            class="form-control @error('accountName') is-invalid @enderror"
                                            placeholder="Enter Price...">
                                        @error('price')
                                            <small class=" invalid-feedback">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="id" value="{{ $data->id }}">
                            <div class="mb-3">
                                <input type="submit" value="Update Payment"
                                    class=" btn btn-primary w-100 rounded shadow-sm">
                            </div>
                        </div>
                    </form>
                        </div>
                    </div>
                </div>


            </div>
        </div>

    </div>
@endsection
