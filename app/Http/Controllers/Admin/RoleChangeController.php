<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class RoleChangeController extends Controller
{
    // direct admin list page
    public function adminList(){
        $data = User::select('id','name','nickname','email','phone','address','created_at','provider','role')
                ->orwhere('role', 'admin')
                ->orwhere('role', 'superadmin')
                ->paginate(5);

        $userCount = User::where('role','user')->count();

        return view('admin.adminList',compact('data','userCount'));
    }


    // direct user list page
    public function userList(){
        $data = User::select('id','name','nickname','email','phone','address','created_at','provider','role')
                ->where('role', 'user')
                ->paginate(5);

        $adminCount = User::orwhere('role','admin')
                            ->orwhere('role','superadmin')
                            ->count();

        return view('admin.userList',compact('data','adminCount'));
    }

    // delete admin account
    public function deleteAdminAccount($id){
        User::where('id',$id)->delete();
        Alert::success('Delete Success', 'Admin Account Deleted Successfully...');
        return back();
    }

    // delete user account
    public function deleteUserAccount($id){
        User::where('id',$id)->delete();
        Alert::success('Delete Success', 'User Account Deleted Successfully...');
        return back();
    }

    // change to admin role
    public function changeAdminRole($id){
        User::where('id',$id)->update(['role'=>'admin']);
        Alert::success('Change Admin Account Success', 'User Account Changes Successfully...');
        return back();
    }

    // change to user role
    public function changeUserRole($id){
        User::where('id',$id)->update(['role'=>'user']);
        Alert::success('Change User Account Success', 'Admin Account Changes Successfully...');
        return back();
    }


}
