<?php

namespace App\Http\Controllers\Api;

use App\Events\NotificationAdminEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Http\Resources\Login;
use App\Models\Admin;
use App\Models\City;
use App\Models\Country;
use App\Models\FCM;
use App\Models\FcmToken;
use App\Models\Intro;
use App\Models\Notification;
use App\Models\NotificationAdmin;
use App\Models\Package;
use App\Models\PackageUser;
use App\Models\Product;
use App\Models\User;
use App\Models\Setting;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function countries(Request $request)
    {
        $countries = Country::query();

        if ($request->with_cities) {
            $countries = $countries->with('cities');
        }
        $countries = $countries->get();
        return mainResponse(true, 'ok', compact('countries'), []);
    }

    public function cities(Request $request)
    {
        $country_uuid = @auth('sanctum')->user()->country_uuid;
        $cities = City::query()->when($country_uuid, function ($q) use ($country_uuid) {
            $q->where('country_uuid', $country_uuid);
        })->paginate();
        $items = pageResource($cities, CityResource::class);
        return mainResponse(true, 'ok', compact('items'), []);
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
        $code = '1111';
        Verification::query()->updateOrCreate([
            'mobile' => $request->mobile,
        ], [
            'code' => Hash::make($code)
        ]);
        return mainResponse(true, 'User Send successfully', [], []);
    }

    public function verifyCode(Request $request)
    {
        $rules = [
            'mobile' => 'required|exists:users,mobile',
            'code' => 'required|string',
            'fcm_token' => 'required',
            'fcm_device' => 'required|in:android,ios'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $item = Verification::query()->where('mobile', $request->mobile)->first();
        if ($item && Hash::check($request->code, $item->code)) {
            $user = User::query()->where('mobile', $request->mobile)->first();
//                $user->setAttribute('token', $user->createToken('api')->plainTextToken);
            $token = $user->createToken('api')->plainTextToken;
            FcmToken::query()->create([
                "user_uuid" => $user->uuid,
                "fcm_device" => $request->fcm_device,
                "fcm_token" => $request->fcm_token
            ]);
            Verification::query()->where('mobile', $request->mobile)->delete();

        } else {
            return mainResponse(false, __('Code is not correct'), [], []);
        }

        $user->setAttribute('token', $token);
        $user = new Login($user);

        return mainResponse(true, __('ok'), $user, []);
    }

    public function again(Request $request)
    {
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
        return mainResponse(true, "done", $code, [], 101);

    }

    public function register(Request $request)
    {

        $rules = [
            'full_mobile' => 'required|string|digits_between:8,14',
            'mobile' => 'required|unique:users,mobile',
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'country_uuid' => 'required|exists:countries,uuid',
            'city_uuid' => 'required|exists:cities,uuid',
            'type' => 'required|in:artist,user',

        ];
        $request->merge([
            'full_mobile' => str_replace('-', '', ($request->mobile)),
        ]);

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }

        $user = User::query()->create($request->only('mobile', 'name', 'email', 'country_uuid', 'city_uuid', 'type'));
//        PackageUser::query()->create([
//            'package_uuid' => Package::BASIC,
//            'user_uuid' => $user->uuid,
//        ]);
        if ($user) {
            $code = rand(1000, 9999);
            $code = '1111';
            Verification::query()->updateOrCreate([
                'mobile' => $request->mobile,
            ], [
                'code' => Hash::make($code)
            ]);
            $token = $user->createToken('api')->plainTextToken;
            $user->setAttribute('token', $token);
            $user = new Login($user);
            $admins_uuid = Admin::query()->whereHas('roles.permissions', function ($query) {
                $query->where('name', 'user')->orWhere('name', 'artisan');
            })->pluck('id')->toArray();
            array_push($admins_uuid, 1);
            if ($request->type == User::USER) {
                $not = $this->sendNotification($user->uuid, User::class, $user->uuid, $admins_uuid, Notification::NEW_USER, 'user', 'admin');
                event(new NotificationAdminEvent('user', $not->content, $not->title, route('users.index') . "?uuid=" . $not->uuid));

            } else {
                $not = $this->sendNotification($user->uuid, User::class, $user->uuid, $admins_uuid, Notification::NEW_ARTIST, 'user', 'admin');
                event(new NotificationAdminEvent('artist', $not->content, $not->title, route('artists.index') . "?uuid=" . $not->uuid));

            }


            return mainResponse(true, __('ok'), $user, []);
        } else {
            return mainResponse(false, __('حصل خطا ما'), [], []);
        }

    }

    public function logout(Request $request)
    {
        $token = $request->bearerToken();
        $user = Auth::guard('sanctum')->user();

        $user->fcm_tokens()->where('fcm_token', $request->fcm_token)->delete();
        if ($token === null) {
            $user->tokens()->delete();
        } else {
            $user->tokens()->where('id', $token)->delete();
        }
        return mainResponse(true, '', [], []);
    }

    public function intros()
    {
        $intros = Intro::all();
        return mainResponse(true, "done", compact('intros'), [], 200);


    }

}
