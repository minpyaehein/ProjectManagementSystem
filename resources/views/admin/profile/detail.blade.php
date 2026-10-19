@extends('admin.layouts.master')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-8 offset-2 card p-3 shadow-sm rounded">
                <div>
                    <h6 class="m-0 text-primary font-weight-bold">Admin Profile</h6>
                </div>
                <form action="{{route('profileUpdate')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="hidden" name="oldImage" value="{{auth()->user()->profile}}">
                            @if (auth()->user()->profile == null)
                                <img class="img-profile mb-1 w-25 rounded" id="output"
                                src="{{ asset('admin/img/undraw_profile_2.svg') }}">
                            @else
                                <img class="img-profile mb-1 w-25 rounded" id="output"
                                src="{{ asset('adminProfile/'.auth()->user()->profile) }}">
                            @endif

                            <input type="file" name="image" id=""
                                class="form-control mt-1 @error('image') is-invalid @enderror" onchange="loadFile(event)">
                            @error('image')
                                <small class=" invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name" value="{{old('name',auth()->user()->name == null ? auth()->user()->nickname : auth()->user()->name)}}"
                                        class="form-control @error('name') is-invalid @enderror"
                                        placeholder="Enter Name..." @if (auth()->user()->provider != 'simple')disabled @endif>
                                    @error('name')
                                        <small class=" invalid-feedback">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" value="{{old('email',auth()->user()->email)}}"
                                        class="form-control @error('email') is-invalid @enderror"
                                        placeholder="Enter email..." @if (auth()->user()->provider != 'simple')disabled @endif>
                                    @error('email')
                                        <small class=" invalid-feedback">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" value="{{old('phone',auth()->user()->phone)}}"
                                        class="form-control @error('phone') is-invalid @enderror"
                                        placeholder="09...">
                                    @error('phone')
                                        <small class=" invalid-feedback">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <input type="text" name="address" value="{{old('address',auth()->user()->address)}}"
                                        class="form-control @error('address') is-invalid @enderror"
                                        placeholder="Enter address...">
                                    @error('address')
                                        <small class=" invalid-feedback">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        @if (auth()->user()->provider=='simple')
                        <a href="{{route('changePassword')}}">Change Password</a>
                        <br><br><br>
                        @endif

                        <div class="mb-3">
                            <input type="submit" value="Update" class=" btn btn-primary w-100 rounded shadow-sm">
                        </div>
                    </div>
                </form>


            </div>

        </div>
    </div>
@endsection
