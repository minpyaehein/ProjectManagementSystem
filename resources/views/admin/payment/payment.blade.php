@extends('admin.layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-4">
                <div class="card p-3 shadow-sm rounded">

                    <form action="{{ route('paymentCreate') }}" method="post">
                        @csrf

                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label class="form-label">Payment Type</label>
                                        <input type="text" name="paymentType" value="{{ old('paymentType') }}"
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
                                        <input type="text" name="accountNumber" value="{{ old('accountNumber') }}"
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
                                        <input type="text" name="accountName" value="{{ old('accountName') }}"
                                            class="form-control @error('accountName') is-invalid @enderror"
                                            placeholder="Enter Price...">
                                        @error('price')
                                            <small class=" invalid-feedback">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <input type="submit" value="Create Product"
                                    class=" btn btn-primary w-100 rounded shadow-sm">
                            </div>
                        </div>
                    </form>


                </div>
            </div>
            <div class="col">
                <table class="table table-hover shadow-sm ">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>ID</th>
                            <th>Payment Type</th>
                            <th>Account Number</th>
                            <th>Account Name</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->type }}</td>
                                <td>{{ $item->account_number}}</td>
                                <td>{{ $item->account_name }}</td>
                                <td>
                                    <a href="{{ route('paymentEdit', $item->id) }}"
                                        class="btn btn-sm btn-outline-secondary"> <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a href="{{ route('paymentDelete', $item->id) }}"
                                        class="btn btn-sm btn-outline-danger"> <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>

                <span class=" d-flex justify-content-end">{{ $data->links() }}</span>
            </div>

        </div>
    </div>
@endsection
