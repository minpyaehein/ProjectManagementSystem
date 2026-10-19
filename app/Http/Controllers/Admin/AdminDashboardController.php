<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    // direct admin dashboard page
    public function index(){
        return view('admin.mainDashboard');
    }
}
