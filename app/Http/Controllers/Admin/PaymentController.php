<?php

namespace App\Http\Controllers\Admin;

use App\Models\payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;

class PaymentController extends Controller
{
    //payment page
    public function paymentPage(){
        $data = payment::orderBy('id', 'asc')->paginate(5);
        return view('admin.payment.payment',compact('data'));
    }

    // create payment
    public function create(Request $request){
        $validator = $request->validate([
                'paymentType' => 'required',
                'accountNumber' => 'required',
                'accountName' => 'required'
            ]);

            payment::create([
                'type' => $request->paymentType,
                'account_number' => $request->accountNumber,
                'account_name' => $request->accountName
            ]);

            Alert::success('Insert Success', 'Payment Inserted Successfully...');

            return back();
    }

    // delete payment
    public function delete($id)
    {
        payment::where('id', $id)->delete();

        Alert::success('Delete Success', 'Payment Delete Successfully...');

        return back();
    }

    // edit payment
    public function edit($id)
    {
        $data = payment::where('id', $id)->first();

        return view('admin.payment.paymentUpdate', compact('data'));
    }

    // payment update
    public function update(Request $request)
    {
        $validator = $request->validate([
            'paymentType' => 'required',
            'accountNumber' => 'required',
            'accountName' => 'required'
        ]);

        payment::where('id', $request->id)->update([
            'type' => $request->paymentType,
            'account_number' => $request->accountNumber,
            'account_name' => $request->accountName
        ]);

        Alert::success('Update Success', 'Payment Update Successfully...');

        return to_route('paymentPage');

    }

}
