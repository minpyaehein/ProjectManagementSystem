<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class AuthController extends Controller
{
    //direct change password
    public function change(){
        return view('admin.password.passwordChange');
    }


    // change password
    public function changePassword(Request $request){
        $validator = $request->validate([
                'oldPassword' => 'required' ,
                'newPassword' => 'required' ,
                'confirmPassword' => 'required|same:newPassword' ,
            ]);

            $dbHashPassword = User::select('password')->where('id',Auth::user()->id)->first();
            $dbHashPassword = $dbHashPassword['password'];

            $userOldPassword = $request->oldPassword; //plane text

            if(Hash::check($userOldPassword, $dbHashPassword)){
                $data = [
                    'password' => Hash::make($request->newPassword)
                ];

                User::where('id', Auth::user()->id)->update($data);
                Alert::success('Password Change Success', 'Password Change Successfully...');

                return back();
            }else{
                Alert::error('Error Message', 'Old Password Do not Match ! Try Again!');
                return back();
            }



        }

}
