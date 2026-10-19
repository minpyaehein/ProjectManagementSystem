@extends('customer.layouts.master')

@section('content')
    <div class="container " style="margin-top: 150px">
        <div class="row">
            <div class="card col-12 shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-5">
                            <h5 class="mb-4">Payment methods</h5>


                            <div class="">
                                <b></b> ( Name : )
                            </div>

                            Account :

                            <hr>

                        </div>
                        <div class="col">
                            <div class="card shadow-sm">
                                <div class="card-header">
                                    Payment Info
                                </div>
                                <div class="card-body">
                                    <div class="">
                                        <form action="" method="post" enctype="multipart/form-data">

                                            <div class="row mt-4">
                                                <div class="col">
                                                    <input type="text" name="name" id="" readonly
                                                        value="" class="form-control " placeholder="User Name...">
                                                </div>
                                                <div class="col">
                                                    <input type="text" name="phone" id="" value=""
                                                        class="form-control " placeholder="09xxxxxxxx">
                                                </div>
                                                <div class="col">
                                                    <input type="text" name="address" id="" value=""
                                                        class="form-control " placeholder="Address...">
                                                </div>
                                            </div>
                                            <div class="row mt-4">
                                                <div class="col">
                                                    <select name="paymentType" id="" class=" form-select ">
                                                        <option value="">Choose Payment methods...</option>

                                                    </select>
                                                </div>
                                                <div class="col">
                                                    <input type="file" name="payslipImage" id=""
                                                        class="form-control ">
                                                </div>
                                            </div>

                                            <div class="row mt-4">
                                                <div class="col">
                                                    <input type="hidden" name="orderCode" value="">
                                                    Order Code : <span class="text-secondary fw-bold"></span>
                                                </div>
                                                <div class="col">
                                                    <input type="hidden" name="totalAmount" value="">
                                                    Total amt : <span class=" fw-bold"> mmk</span>
                                                </div>
                                            </div>

                                            <div class="row mt-4 mx-2">
                                                <button type="submit" class="btn btn-outline-success w-100"><i
                                                        class="fa-solid fa-cart-shopping me-3"></i> Order
                                                    Now...</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
