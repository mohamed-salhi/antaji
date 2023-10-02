<?php

namespace App\Http\Controllers\Admin\setting;

use App\Http\Controllers\Admin\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Upload;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:setting', ['only' => ['index','store','create','destroy','edit','update']]);
    }
    public function index()
    {
        $settings = Setting::query()->first();
        return view('admin.settings.index', compact('settings'));
    }

    public function post(Request $request)
    {

        $rules = [];
        foreach (locales() as $key => $language) {
            $rules['title_page_' . $key] = 'required|string|max:255';
        }
        $rules['commission'] = 'required';


        $this->validate($request, $rules);
        $data = [];
        foreach (locales() as $key => $language) {
            $data['title_page'][$key] = $request->get('title_page_' . $key);
        }
        $data['commission'] = $request->commission;

        $setting = Setting::query()->updateOrCreate(['id' => 1], $data);
        if ($request->hasFile('image')) {
            UploadImage($request->image, "upload/setting/", Setting::class, $setting->id, true, null, Upload::IMAGE, 'home_page_title');
        }
        return response()->json([
            'item_edited'
        ]);

    }


    public function terms_conditions()
    {
        $settings = Page::query()->where('id',Page::terms_conditions)->first();
        return view('admin.settings.terms_conditions', compact('settings'));
    }

    public function terms_conditions_post(Request $request)
    {

        $rules = [];
        foreach (locales() as $key => $language) {
            $rules['terms_conditions_' . $key] = 'required';
        }
        $this->validate($request, $rules);
        $data = [];
        foreach (locales() as $key => $language) {
            $data['title'][$key] = $request->get('terms_conditions_' . $key);
        }
        $setting = Page::query()->updateOrCreate(['id' => Page::terms_conditions], $data);
        if ($setting) {
            return redirect()->back()->with('done', "done");
        } else {
            return redirect()->back()->with('done', "err");;
        }
    }

    public function about_application()
    {
        $settings = PAge::query()->where('id',Page::about_application)->first();
        return view('admin.settings.about_application', compact('settings'));
    }

    public function about_application_post(Request $request)
    {

        $rules = [];
        foreach (locales() as $key => $language) {
            $rules['about_application_' . $key] = 'required';
        }
        $this->validate($request, $rules);
        $data = [];
        foreach (locales() as $key => $language) {
            $data['title'][$key] = $request->get('about_application_' . $key);
        }
        $setting = Page::query()->updateOrCreate(['id' => Page::about_application], $data);
        if ($setting) {
            return redirect()->back()->with('done', "done");
        } else {
            return redirect()->back()->with('done', "err");;
        }
    }

    public function policies_privacy()
    {
        $settings = Page::query()->where('id',Page::policies_privacy)->first();
        return view('admin.settings.policies_privacy', compact('settings'));
    }

    public function policies_privacy_post(Request $request)
    {

        $rules = [];
        foreach (locales() as $key => $language) {
            $rules['policies_privacy_' . $key] = 'required';
        }
        $this->validate($request, $rules);
        $data = [];
        foreach (locales() as $key => $language) {
            $data['title'][$key] = $request->get('policies_privacy_' . $key);
        }
        $setting = Page::query()->updateOrCreate(['id' => Page::policies_privacy], $data);
        if ($setting) {
            return redirect()->back()->with('done', "done");
        } else {
            return redirect()->back()->with('done', "err");;
        }
    }

    public function delete_my_account()
    {
         $settings = Page::query()->where('id',Page::delete_my_account)->first();

        return view('admin.settings.delete_my_account', compact('settings'));
    }

    public function delete_my_account_post(Request $request)
    {

        $rules = [];
        foreach (locales() as $key => $language) {
            $rules['delete_my_account_' . $key] = 'required';
        }
        $this->validate($request, $rules);
        $data = [];
        foreach (locales() as $key => $language) {
            $data['title'][$key] = $request->get('delete_my_account_' . $key);
        }
        $setting = Page::query()->updateOrCreate(['id' => Page::delete_my_account], $data);
        if ($setting) {
            return redirect()->back()->with('done', "done");
        } else {
            return redirect()->back()->with('done', "err");
        }
    }
}
