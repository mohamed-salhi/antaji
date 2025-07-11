<?php

namespace App\Http\Controllers\Admin\Intro;

use App\Http\Controllers\Controller;
use App\Models\Intro;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class IntroController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:intro', ['only' => ['index','store','create','destroy','edit','update']]);
    }
   public function index(){
       return view('admin.intros.index');
   }
    public function store(Request $request)
    {

        $rules = [];
        foreach (locales() as $key => $language) {
            $rules['title_' . $key] = 'required|string|max:255';
            $rules['sup_title_' . $key] = 'required|string|max:255';
        }
        $rules['image']='required|image';
        $this->validate($request, $rules);
        $data = [];

        foreach (locales() as $key => $language) {
            $data['title'][$key] = $request->get('title_' . $key);
            $data['sup_title'][$key] = $request->get('sup_title_' . $key);

        }

        $intro=  Intro::query()->create($data);

        if ($request->hasFile('image')) {
            UploadImage($request->image, "upload/intro/", Intro::class, $intro->uuid, false ,null,Upload::IMAGE);
        }

        return response()->json([
            'item_added'
        ]);

    }


    public function update(Request $request)
    {
        $intro= Intro::findOrFail($request->uuid);

//        Gate::authorize('place.update');
        $rules = [];
        foreach (locales() as $key => $language) {
            $rules['title_' . $key] = 'required|string|max:255';
            $rules['sup_title_' . $key] = 'required|string|max:255';
        }
        $rules['image']='nullable|image';
        $this->validate($request, $rules);
        $data = [];
        foreach (locales() as $key => $language) {
            $data['title'][$key] = $request->get('title_' . $key);
            $data['sup_title'][$key] = $request->get('sup_title_' . $key);
        }
        $intro->update($data);
        if ($request->hasFile('image')) {
            UploadImage($request->image, "upload/intro/", Intro::class, $intro->uuid, true,null,Upload::IMAGE);
        }

        return response()->json([
            'item_edited'
        ]);

    }

    public function destroy($uuid)
    {

        try {
            $uuids=explode(',', $uuid);
            $intro=  Intro::whereIn('uuid', $uuids)->get();
            foreach ($intro as $item){
                Storage::delete('public/' . @$item->imageIntro->path);

//                File::delete(public_path('upload/intro/'.$item->imageIntro->filename));
                $item->imageIntro()->delete();
                $item->delete();
            }
            return response()->json([
                'item_deleted'
            ]);
        }catch (\Exception $e){
            return response()->json([
                'err'
            ]);
        }
    }

    public function indexTable(Request $request)
    {
        $intro = Intro::query()->withoutGlobalScope('status')->orderByDesc('created_at');
        return Datatables::of($intro)
            ->addColumn('checkbox',function ($que){
                return $que->uuid;
            })
            ->addColumn('action', function ($que) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $que->uuid . '" ';
                $data_attr .= 'data-image="' . $que->image . '" ';
                foreach (locales() as $key => $value) {
                    $data_attr .= 'data-sup_title_' . $key . '="' . $que->getTranslation('sup_title', $key) . '" ';
                    $data_attr .= 'data-title_' . $key . '="' . $que->getTranslation('title', $key) . '" ';

                }
                $user = Auth()->user();

                $string = '';
//                if ($user->can('competitions-edit')){
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary btn_edit" data-toggle="modal"
                    data-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
//                }
//                if ($user->can('competitions-delete')){
                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger btn_delete" data-uuid="' . $que->uuid .
                    '">' . __('delete') . '</button>';
//                }
                return $string;
            })->addColumn('status', function ($que) {
                $currentUrl = url('/');
                if ($que->status == 1) {
                    $data = '
<button type="button"  data-url="' . $currentUrl . "/admin/intros/updateStatus/0/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-success " data-uuid="' . $que->uuid .
                        '">' . __('active') . '</button>
                    ';
                } else {
                    $data = '
<button type="button"  data-url="' . $currentUrl . "/admin/intros/updateStatus/1/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-danger " data-uuid="' . $que->uuid .
                        '">' . __('inactive') . '</button>
                    ';
                }
                return $data;
            })
            ->rawColumns(['action', 'status'])->toJson();
    }
    public function updateStatus($status,$sup)
    {
        $uuids=explode(',', $sup);

        $activate =  Intro::query()->withoutGlobalScope('status')
            ->whereIn('uuid',$uuids)
            ->update([
                'status'=>$status
            ]);
        return response()->json([
            'item_edited'
        ]);
    }
}
