<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\products;

class UserDashboardController extends Controller
{
    // direct user dashboard page
    public function index(){
        $category = Category::get();
        $products = products::get();
        return view('customer.home', compact('category','products'));
    }
}
