<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class ProfileController extends Controller
{
    // direct profile details
    public function profileDetail()
    {
        return view('admin.profile.detail');
    }

    // create admin account
    public function createAdminAccount(){
        return view('admin.profile.createAdminAccount');
    }

    // create new admin account
    public function create(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password'=> 'required',
            'confirmPassword'=> 'required|same:password',
        ]);

        $adminAccount = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
            'provider' => 'simple',
        ];

        User::create($adminAccount);

        Alert::success('Update Success', 'New Admin Create Successfully...');

        return back();
    }


    // update profile
    public function profileUpdate(Request $request)
    {
        $this->validationCheck($request);
        $adminData = $this->requestAdminData($request);

        if($request->hasFile('image')){
        // delete old image
        if($request->oldImage != null){
            if(file_exists(public_path('adminProfile/'.$request->oldImage))){
            unlink(public_path('adminProfile/'.$request->oldImage));
        }
        }

        // upload new image
        $fileName = uniqid() . $request->file('image')->getClientOriginalName();
        $request->file('image')->move(public_path().'/adminProfile/',$fileName);
        $adminData['profile'] = $fileName;

        }else {
            $adminData['profile'] = $request->oldImage;
        }

        User::where('id',Auth::user()->id)->update($adminData);

        Alert::success('Update Success', 'Profile Update Successfully...');

        return back();

    }

    // request admin data
    private function requestAdminData($request)
    {
        $data = [];

        if (Auth::user()->name != null) {
            $data['name'] = Auth::user()->provider == 'simple' ? $request->name : Auth::user()->name;
        } else {
            $data['nickname'] = Auth::user()->provider == 'simple' ? $request->name : Auth::user()->name;
        }

        $data['email']   = Auth::user()->provider == 'simple' ? $request->email : Auth::user()->email;
        $data['phone']   = $request->phone;
        $data['address'] = $request->address;

        return $data;
    }

    // create | update validation check
    private function validationCheck($request)
    {
        $rules = [
            'phone'   => 'required|unique:users,phone,'.Auth::user()->id,
            'address' => 'required',
            'image' => 'mimes:png,jpg,jpeg,bmp|file'
        ];

        // $rules['email'] = Auth::user()->provider != 'simple' ? 'unique:users,email,' . Auth::user()->id : 'required|unique:users,email,' . Auth::user()->id;

        if(Auth::user()->provider == 'simple'){
            $rules['name'] = 'required';
            $rules['email'] = 'required|unique:users,email,'.Auth::user()->id;
        }

        $message = [];

        $validator = $request->validate($rules, $message);

    }

}
