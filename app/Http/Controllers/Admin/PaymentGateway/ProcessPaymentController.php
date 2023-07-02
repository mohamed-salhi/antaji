<?php

namespace App\Http\Controllers\Admin\paymentGateway;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Movement;
use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProcessPaymentController extends Controller
{
    public function index(){
        return view('admin.paymentGateways.process');
    }
    public function getData(Request $request)
    {
        $countrys = Payment::query()->where('status',Payment::COMPLETE);
        return Datatables::of($countrys)
            ->filter(function ($query) use ($request) {
                if ($request->user_name){
                    $query->whereHas('user',function ($query)use ($request){
                        $query->where('name','like',"%{$request->user_name}%");
                    });
                }
                if ($request->user_phone){
                    $query->whereHas('user',function ($query)use ($request){
                        $query->where('phone', 'like', "%{$request->user_phone}%");
                    });
                }
                if ($request->date) {
                    $query->where('created_at', $request->date);
                }
                if ($request->balance) {
                    $query->where('price', $request->balance);
                }
                if ($request->payment_method_uuid) {
                    $query->where('payment_method_uuid', $request->payment_method_uuid);
                }
                if ($request->process) {
                    if ($request->process==2){
                        $query->where('reference_type', Competition::class);
                    }elseif($request->process==1){
                        $query->where('reference_type', Movement::class);

                    }
                }

            })
            ->toJson();

    }

}
