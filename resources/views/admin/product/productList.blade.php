@extends('admin.layouts.master')

@section('content')
    <div class="container">
        <div class=" d-flex justify-content-between my-2">
            <div class="">
                <button class=" btn btn-secondary rounded shadow-sm"> <i class="fa-solid fa-database"></i>
                    Product Count ( ) </button>
                <a href="" class=" btn btn-outline-primary  rounded shadow-sm">All Products</a>
                <a href="" class=" btn btn-outline-danger  rounded shadow-sm">Low Amount Product List</a>
            </div>
            <div class="">
                <form action="{{ route('productList') }}" method="get">
                    <div class="input-group">
                        <input type="text" name="searchKey" value="{{request('searchKey')}}" class=" form-control"
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
                            <th>Image</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Category</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($products as $item)
                            <tr>
                            <td> <img src="{{ asset('productImages/'.$item->image) }}" class=" img-thumbnail rounded shadow-sm" style="width:100px"
                                    alt="">
                            </td>
                            <td>{{$item->name}}</td>
                            <td>{{$item->price}} mmk</td>
                            <td class="col-2">
                                {{-- <button type="button" class="btn btn-secondary position-relative">


                                    <span
                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        Low amt stock
                                    </span>

                                </button> --}}
                                {{$item->count}}
                            </td>
                            <td>{{$item->categoryName}}</td>
                            <td>

                                <a href="{{route('productDetails', $item->id)}}" class="btn btn-sm btn-outline-primary"> <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{route('productEdit', $item->id)}}" class="btn btn-sm btn-outline-secondary"> <i
                                        class="fa-solid fa-pen-to-square"></i> </a>
                                <a href="{{route('productDelete', $item->id)}}" class="btn btn-sm btn-outline-danger"> <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>

                        </tr>
                        @endforeach

                    </tbody>
                </table>

                    <span class=" d-flex justify-content-end">{{ $products->links() }}</span>


            </div>
        </div>
    </div>
@endsection
