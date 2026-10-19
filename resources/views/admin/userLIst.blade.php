@extends('admin.layouts.master')

@section('content')
    <div class="container">
        <div class=" d-flex justify-content-between my-2">
            <div>
                <a href="{{ route('adminList') }}"> <button class=" btn btn-sm btn-secondary  "> Admin List <span class="badge badge-light">{{$adminCount}}</span></button> </a>
                <a href="{{ route('userList') }}"> <button class=" btn btn-sm btn-secondary  "> User List <span class="badge badge-light">{{$data->total()}}</span></button> </a>
            </div>
            <div class="">
                <form action="{{ route('userList') }}" method="get">

                    <div class="input-group">
                        <input type="text" name="searchKey" value="{{ request('searchKey') }}" class=" form-control"
                            placeholder="Enter Search Key...">
                        <button type="submit" class=" btn bg-dark text-white"> <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <table class="table table-hover shadow-sm ">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Phone</th>
                            <th> Platform</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                        <tr>
                            <td>{{$item->id}}</td>
                            <td>
                                @if ($item->name != null)
                                    {{$item->name}}
                                @else
                                    {{$item->nickname}}
                                @endif
                            </td>
                            <td>{{$item->email}}</td>
                            <td>{{$item->address}}</td>
                            <td>{{$item->phone}}</td>
                            <td>{{$item->provider}}</td>
                            <td>
                            @if(auth()->user()->role == 'superadmin')
                                <a href="{{ route('deleteUserAccount', $item->id) }}">
                                    <button class="btn btn-sm bg-danger text-white rounded shadow-sm"><i class="fa-solid fa-trash"></i></button>
                                </a>
                                <a href="{{ route('changeAdminRole', $item->id) }}">
                                    <button class="btn btn-sm bg-dark text-white rounded shadow-sm">Change to admin role <i class="fa-solid fa-angles-up"></i></button>
                                </a>
                            @endif
                            </td>
                        </tr>

                        @endforeach
                    </tbody>
                </table>
                    <div class="d-flex justify-content-end">{{ $data->links() }}</div>

                <span class=" d-flex justify-content-end"></span>

            </div>
        </div>
    </div>
@endsection
