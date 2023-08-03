<?php

namespace App\Http\Controllers\Admin\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProcessPaymentController extends Controller
{
    public function index(){
        $method=PaymentGateway::all();
        return view('admin.paymentGateways.process',compact('method'));
    }
    public function getData(Request $request)
    {
        $countrys = Payment::query()->orderByDesc('created_at');
        return Datatables::of($countrys)
            ->filter(function ($query) use ($request) {
                if ($request->user_name){
                    $query->whereHas('user',function ($query)use ($request){
                        $query->where('name','like',"%{$request->user_name}%");
                    });
                }
                if ($request->user_phone){
                    $query->whereHas('user',function ($query)use ($request){
                        $query->where('mobile', 'like', "%{$request->user_phone}%");
                    });
                }
                if ($request->date) {
                    $query->where('created_at', $request->date);
                }
                if ($request->price) {
                    $query->where('price', $request->price);
                }
                if ($request->payment_method_id) {
                    $query->where('payment_method_id', $request->payment_method_id);
                }
                if ($request->order_number) {
                        $query->where('order_number',$request->order_number);
                }

            })
            ->toJson();

    }

}
