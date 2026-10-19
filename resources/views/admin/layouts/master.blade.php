<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>POS Admin Dashboard</title>

    <!-- Custom fonts for this template-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('admin/css/sb-admin-2.min.css') }}" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Point of Sales</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href=""><i class="fas fa-fw fa-table"></i><span>Dashboard </span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('categoryList') }}"><i
                        class="fa-solid fa-circle-plus"></i></i><span>Category </span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('productCreate') }}"><i class="fa-solid fa-plus"></i></i><span>Add
                        Products </span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('productList') }}"><i
                        class="fa-solid fa-layer-group"></i><span>Product List </span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{route('paymentPage')}}"><i class="fa-solid fa-credit-card"></i></i><span>Payment Method
                    </span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="#"><i class="fa-solid fa-list"></i><span>Sale Information </span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href=""><i class="fa-solid fa-cart-shopping"></i><span>Order Board
                    </span></a>
            </li>

            @if (auth()->user()->provider == 'simple')
            <li class="nav-item">
                <a class="nav-link" href="{{route('passwordChange')}}"><i class="fa-solid fa-lock"></i></i></i><span>Change Password
                    </span></a>
            </li>
            @endif

            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <span class="nav-link">
                        <button type="submit" class="btn bg-dark text-white">
                            <i class="fa-solid fa-right-from-bracket"></i> Logout</button>
                    </span>
                </form>
            </li>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">



                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">


                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    @if (auth()->user()->name != null)
                                        {{ auth()->user()->name }}
                                    @else
                                        {{ auth()->user()->nickname }}
                                    @endif
                                </span>
                            @if (auth()->user()->profile == null)
                                <img class="img-profile mb-1 w-25 rounded"
                                src="{{ asset('admin/img/undraw_profile_2.svg') }}">
                            @else
                                <img class="img-profile mb-1 w-25 rounded"
                                src="{{ asset('adminProfile/'.auth()->user()->profile) }}">
                            @endif
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{{ route('profileDetail')}}">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>

                                @if (auth()->user()->role == 'superadmin')
                                <a class="dropdown-item" href="{{ route('createAdminAccount') }}">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Add New Admin Account
                                </a>
                                @endif

                                <a class="dropdown-item" href="{{ route('adminList') }}">
                                    <i class="fas fa-users fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Admin List
                                </a>

                                <a class="dropdown-item" href="{{ route('userList')}}">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    User List
                                </a>

                                @if (auth()->user()->provider == 'simple')
                                <a class="dropdown-item" href="{{route('passwordChange')}}">
                                    <i class="fa-solid fa-lock fa-sm fa-fw mr-2 text-gray-400"></i></i></i>
                                    Change Password
                                </a>
                                @endif

                                <div class="dropdown-divider"></div>
                                <span class="dropdown-item" data-toggle="modal" data-target="#logoutModal">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <input type="submit" class="btn btn-primary text-white w-100"
                                            value="Logout">
                                    </form>
                                </span>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                @yield('content')

                @include('sweetalert::alert')


                <!-- Bootstrap core JavaScript-->
                <script src="{{ asset('admin/vendor/jquery/jquery.min.js') }}"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
                    integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
                    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
                <script src="{{ asset('admin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

                <!-- Core plugin JavaScript-->
                <script src="{{ asset('admin/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

                <!-- Custom scripts for all pages-->
                <script src="{{ asset('admin/js/sb-admin-2.min.js') }}"></script>


                <script src="{{ asset('admin/vendor/chart.js/Chart.min.js') }}"></script>

                <!-- Page level custom scripts -->
                <script src="{{ asset('admin/js/demo/chart-area-demo.js') }}"></script>
                <script src="{{ asset('admin/js/demo/chart-pie-demo.js') }}"></script>

                <script>
                    function loadFile(event) {
                        var reader = new FileReader();

                        reader.onload = function() {
                            var output = document.getElementById('output');
                            output.src = reader.result;
                        }

                        reader.readAsDataURL(event.target.files[0]);
                    }
                </script>


</body>

</html>
