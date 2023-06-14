<?php

namespace App\Http\Controllers\Api;

use App\Events\NotificationAdminEvent;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\FCM;
use App\Models\FcmToken;
use App\Models\Intro;
use App\Models\NotificationAdmin;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function first()
    {
        return mainResponse(true, 'ok', Country::all(), []);
    }
    public function login(Request $request)
    {
        $rules = [
            'mobile' => 'required|exists:users,mobile',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $code = rand(1000, 9999);
        Verification::query()->updateOrCreate([
            'mobile' => $request->mobile,
        ], [
            'code' => Hash::make($code)
        ]);
        return mainResponse(true, 'User Send successfully', compact('code'), []);
    }
    public function verifyCode(Request $request)
    {
        $rules = [
            'mobile' => 'required|exists:users,mobile',
            'code' => 'required|string',
            'fcm_token'=>'required',
            'fcm_device'=>'required|in:android,ios'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $item = Verification::query()->where('mobile', $request->mobile)->first();
        if ($item && Hash::check($request->code, $item->code)) {
            $user = User::query()->where('mobile', $request->mobile)->first();
                $user->setAttribute('token', $user->createToken('api')->plainTextToken);
                FcmToken::query()->create([
                    "user_uuid"=>$user->uuid,
                    "fcm_device"=>$request->fcm_device,
                    "fcm_token"=>$request->fcm_token
                ]);
                Verification::query()->where('mobile', $request->mobile)->delete();

        } else {
            return mainResponse(false, __('Code is not correct'), [], []);
        }

        return mainResponse(true, __('ok'), compact('user'), []);
    }
    public function again(Request $request){
        $rules = [
            'mobile' => 'required|exists:users,mobile',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $code = strval(rand(1000, 9999));
        Verification::query()->updateOrCreate([
            'mobile' => $request->mobile,
        ], [
            'code' => Hash::make($code)
        ]);
        return mainResponse(true,"done", $code, [], 101);

    }
    public function register(Request $request)
    {
        $rules = [
            'mobile' => 'required|unique:users,mobile|max:12',
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'country_uuid' => 'required|exists:countries,uuid',
            'city_uuid' => 'required|exists:cities,uuid',
            'type' => 'required|in:artist,user',

        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }

        $user = User::query()->create($request->only('mobile', 'name', 'email', 'country_uuid', 'city_uuid','type'));

            if ($user) {
                $code = rand(1000, 9999);
                Verification::query()->updateOrCreate([
                    'mobile' => $request->mobile,
                ], [
                    'code' => Hash::make($code)
                ]);
                return mainResponse(true, __('ok'), compact('code','user'), []);
            } else {
                return mainResponse(false, __('حصل خطا ما'), [], []);
            }

    }
    public function logout($fsm=null,$token=null){

        $user=Auth::guard('sanctum')->user();
      
        $user->fcm_tokens()->where('fcm_token',$fsm)->delete();
        if($token===null){
            $user->tokens()->delete();
            return response()->json(['message' => 'User successfully signed out','status'=>200]);
        }else{
            $user->tokens()->where('id', $token)->delete();
            return response()->json(['message' => 'User successfully signed out vvv','status'=>200]);
        }

    }
    public function intros(){
        $intros=Intro::all();
        return mainResponse(true, "done", compact('intros'), [], 200);


    }
}
