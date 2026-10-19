@extends('admin.layouts.master')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-8 offset-2 card p-3 shadow-sm rounded">
                <div class="card-body">
                    <div class="mb-3">
                        <img class="img-profile mb-1 w-25 rounded" id="output"
                            src="{{ asset('productImages/' . $data->image) }}">
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <h4>{{ $data->name }}</h4>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label class="form-label">Category Name</label>
                                <h4>{{$data->categoryName}}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="mb-3">
                                <label class="form-label">Price</label>
                                <h4>{{ $data->price }}</h4>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label class="form-label">Count</label>
                                <h4>{{ $data->count }}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <h4>{{ $data->description }}</h4>
                    </div>
                    <div class="mb-3">
                        <a href="{{route('productList')}}"> <input type="button" value="Back"
                                class=" btn btn-primary w-100 rounded shadow-sm">
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection
