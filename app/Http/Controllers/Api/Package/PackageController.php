<?php

namespace App\Http\Controllers\Api\Package;

use App\Http\Controllers\Controller;
use App\Http\Resources\PackageResources;
use App\Models\Package;
use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PackageController extends Controller
{
   public function getPackages(){
      $item= Package::query()->select('uuid','name','details','price')->get();
       return mainResponse(true, "done", compact('item'), [], 200);
   }
   public function payment($uuid){
      $package= Package::query()->select('uuid','price','name')->findOrFail($uuid);
      $package=PackageResources::make($package);
       $payment_gateway = PaymentGateway::all();
       $bill = [
          [
              'title' => __('package price'),
              'amount' => $package->price,
          ],
          [
           'title' => __('total'),
           'amount' => $package->price,
       ]
       ];
       return mainResponse(true, 'ok', compact('package','payment_gateway', 'bill'), []);
   }
    public function checkout(Request $request)
    {
        $rules = [
            'package_uuid' => 'required|exists:packages,uuid',
            'payment_method_id' => 'required|exists:packages,uuid',

        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }

        $user = Auth::guard('sanctum')->user();

        $package= Package::query()->findOrFail($request->package_uuid);

        if ($request->payment_method_id == PaymentGateway::MADA) {
            $amount = $package->price;
            $url = "https://eu-test.oppwa.com/v1/checkouts";
            $data = "entityId=8a8294174b7ecb28014b9699220015ca" .
                "&amount=$amount" .
                "&currency=EUR" .
                "&paymentType=DB";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization:Bearer OGE4Mjk0MTc0YjdlY2IyODAxNGI5Njk5MjIwMDE1Y2N8c3k2S0pzVDg='));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $responseData = curl_exec($ch);
            if (curl_errno($ch)) {
                return curl_error($ch);
            }
            curl_close($ch);
            $data = json_decode($responseData);
//            return $data;
            $id = $data->id;

        }
        elseif ($request->payment_method_id == PaymentGateway::ABLEPAY) {
            $amount = $package->price;
            $url = "https://eu-test.oppwa.com/v1/checkouts";
            $data = "entityId=8a8294174b7ecb28014b9699220015ca" .
                "&amount=$amount" .
                "&currency=EUR" .
                "&paymentType=DB";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization:Bearer OGE4Mjk0MTc0YjdlY2IyODAxNGI5Njk5MjIwMDE1Y2N8c3k2S0pzVDg='));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $responseData = curl_exec($ch);
            if (curl_errno($ch)) {
                return curl_error($ch);
            }
            curl_close($ch);
            $data = json_decode($responseData);
            $id = $data->id;

        }
        else {
            return mainResponse(false, "payment_method not found", [], [], 404);

        }

        $payment = Payment::query()->updateOrCreate([
            'user_uuid' => $user->uuid,
            'price' => $package->price,
            'status' => Payment::PENDING,
            'payment_method_id' => $request->payment_method_id,
        ], [
            'order_number' => Carbon::now()->timestamp . '' . rand(1000, 9999),
            'transaction_id' => $id,
            'package_uuid'=>$package->uuid,

        ]);

        $url = route('paymentGateways.checkout', $payment->uuid);
        $status = 'url';

        return mainResponse(true, 'ok', compact('status', 'url'), []);
    }

}
