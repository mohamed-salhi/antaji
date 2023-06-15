<?php

namespace App\Http\Controllers\Admin\setting;

use App\Http\Controllers\Admin\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(){
        $settings=Setting::query()->first();
        return view('admin.settings.index',compact('settings'));
    }

    public function store(Request $request){

                $rules = [];
        $rules['about_application']='required';
        $rules['policies_privacy']='required';
        $rules['terms_conditions']='required';
        $this->validate($request, $rules);
        $setting=  Setting::query()->updateOrCreate([
            'id'=>1
        ],[
            'terms_conditions'=>$request->terms_conditions,
            'about_application'=>$request->about_application,
            'policies_privacy'=>$request->policies_privacy,
            'delete_my_account'=>$request->delete_my_account,

        ]);
        if ($setting){
            return redirect()->back()->with('done',"done");
        }else{
            return redirect()->back()->with('done',"err");;
        }
    }
}
