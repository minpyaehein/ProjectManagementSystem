@extends('admin.layouts.master')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-10 offset-1 card p-3 shadow-sm rounded">

                <form action="{{ route('product_create') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="card-body row">
                        <div class="col-4">
                            <div class="mb-3">
                                <img class="img-profile mb-1 w-100 rounded" id="output"
                                    src="{{ asset('admin/img/default.png') }}">
                                <input type="file" name="image" id=""
                                    class="form-control mt-1 @error('image') is-invalid @enderror" onchange="loadFile(event)">
                                @error('image')
                                    <small class=" invalid-feedback">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="col">
                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label class="form-label">Name</label>
                                        <input type="text" name="name" value="{{ old('name') }}"
                                            class="form-control @error('name') is-invalid @enderror"
                                            placeholder="Enter Name...">
                                        @error('name')
                                            <small class=" invalid-feedback">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-3">
                                        <label class="form-label">Category Name</label>
                                        <select name="categoryName" id=""
                                            class="form-control @error('categoryName') is-invalid @enderror">
                                            <option value="">Choose Category...</option>
                                            @foreach ($categories as $item)
                                                <option value="{{ $item->id }}"
                                                    @if (old('categoryName') == $item->id) selected @endif>{{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('categoryName')
                                            <small class=" invalid-feedback">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label class="form-label">Price</label>
                                        <input type="text" name="price" value="{{ old('price') }}"
                                            class="form-control @error('price') is-invalid @enderror"
                                            placeholder="Enter Price...">
                                        @error('price')
                                            <small class=" invalid-feedback">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-3">
                                        <label class="form-label">Count</label>
                                        <input type="text" name="count" value="{{ old('count') }}"
                                            class="form-control @error('count') is-invalid @enderror"
                                            placeholder="Enter Count...">
                                        @error('count')
                                            <small class=" invalid-feedback">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" id="" cols="30" rows="10"
                                    class="form-control @error('description') is-invalid @enderror" placeholder="Enter Description...">{{ old('description') }}</textarea>
                                @error('description')
                                    <small class=" invalid-feedback">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <input type="submit" value="Create Product" class=" btn btn-primary w-100 rounded shadow-sm">
                            </div>
                        </div>


                    </div>
                </form>


            </div>

        </div>
    </div>
@endsection
