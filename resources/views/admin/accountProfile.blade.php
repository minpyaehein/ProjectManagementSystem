@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">

    <!-- DataTales Example -->
    <div class="card shadow mb-4 col">
        <div class="card-header py-3">
            <div class="">
                <div class="">
                    <h6 class="m-0 font-weight-bold text-primary">Account Information</h6>
                </div>
            </div>
        </div>
        <form>
            <div class="card-body">
                <div class="row ">
                    <div class="col-3 offset-1">

                        <img class="img-profile mt-4 w-100" id="output" src="">


                    </div>
                    <div class="col offset-1">

                        <div class="row mt-3">
                            <div class="col-2 h5">Name :</div>
                            <div class="col h5">Si Thu</div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-2 h5">Email :</div>
                            <div class="col h5">sithu@gmail.com</div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-2 h5">Phone :</div>
                            <div class="col h5">09421077822</div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-2 h5">Addr :</div>
                            <div class="col h5">Yangon</div>

                        </div>

                        <div class="row mt-3">
                            <div class="col-2 h5">Role :</div>
                            <div class="col h5 text-danger">Admin Account</div>
                        </div>

                        <a href="" class=" btn bg-dark text-white btn-sm mt-3 rounded shadow-sm"><i
                                class="fa-solid fa-lock"></i> Change Password</a>
                        <a href="" class=" btn bg-primary text-white btn-sm mt-3 rounded shadow-sm"> <i
                                class="fa-solid fa-pen-to-square"></i> Edit Profile</a>


                    </div>
                </div>
            </div>
        </form>
    </div>

</div>
@endsection
