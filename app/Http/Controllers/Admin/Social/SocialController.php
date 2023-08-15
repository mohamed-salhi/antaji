<?php

namespace App\Http\Controllers\Admin\Social;

use App\Http\Controllers\Controller;
use App\Models\Social;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class SocialController extends Controller
{
//    use ResponseTrait;
//    function __construct()
//    {
//        $this->middleware('permission:place-list|place-edit|place-delete', ['only' => ['index','store','activate']]);
//        $this->middleware('permission:place-create', ['only' => ['store']]);
//        $this->middleware('permission:place-edit', ['only' => ['update','activate']]);
//        $this->middleware('permission:place-delete', ['only' => ['destroy']]);
//    }

    public function index(Request $request)
    {
        return view('admin.social.index');
    }

    public function store(Request $request)
    {
        $rules = [];
        foreach (locales() as $key => $language) {
            $rules['name_' . $key] = 'required|string|max:255';
        }
        $rules['link'] = 'required';
        $rules['image'] = 'required|image';
        $this->validate($request, $rules);
        $data = [];
        foreach (locales() as $key => $language) {
            $data['name'][$key] = $request->get('name_' . $key);
        }
        $data['link'] = $request->link;
        $social = Social::query()->create($data);
        if ($request->hasFile('image')) {
            UploadImage($request->image, Social::PATH, Social::class, $social->uuid, false, null, Upload::IMAGE);
        }

        return response()->json([
            'item_added'
        ]);

    }


    public function update(Request $request)
    {
        $social = Social::findOrFail($request->uuid);

        $rules = [];
        foreach (locales() as $key => $language) {
            $rules['name_' . $key] = 'required|string|max:255';
        }
        $rules['link'] = 'required';

        $rules['image'] = 'nullable|image';
        $this->validate($request, $rules);
        $data = [];
        foreach (locales() as $key => $language) {
            $data['name'][$key] = $request->get('name_' . $key);
        }
        $data['link'] = $request->link;
        if ($request->hasFile('image')) {
            UploadImage($request->image, Social::PATH, Social::class, $social->uuid, true, null, Upload::IMAGE);
        }
        $social->update($data);
        return response()->json([
            'item_edited'
        ]);

    }

    public function destroy($uuid)
    {

        try {
            $uuids = explode(',', $uuid);
            $social = Social::query()->whereIn('uuid', $uuids)->get();

            foreach ($social as $item) {
                Storage::delete('public/' . $item->imageSocial->path);
                $item->imageSocial()->delete();
                $item->delete();
            }
            return response()->json([
                'item_deleted'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'err'
            ]);
        }
    }

    public function indexTable(Request $request)
    {
        $socials = Social::query()->withoutGlobalScope('status')->orderByDesc('created_at');
        return Datatables::of($socials)
            ->filter(function ($query) use ($request) {
                if ($request->status) {
                    ($request->status == 1) ? $query->where('status', $request->status) : $query->where('status', 0);
                }
                if ($request->name) {
                    $query->where('name->' . locale(), 'like', "%{$request->name}%");

                    foreach (locales() as $key => $value) {
                        if ($key != locale())
                            $query->orWhere('name->' . $key, 'like', "%{$request->name}%");
                    }

                }
            })
            ->addColumn('checkbox', function ($que) {
                return $que->uuid;
            })
            ->addColumn('action', function ($que) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $que->uuid . '" ';
                $data_attr .= 'data-link="' . $que->link . '" ';
                $data_attr .= 'data-image="' . $que->image . '" ';
                foreach (locales() as $key => $value) {
                    $data_attr .= 'data-name_' . $key . '="' . $que->getTranslation('name', $key) . '" ';
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
<button type="button"  data-url="' . $currentUrl . "/admin/social/updateStatus/0/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-success " data-uuid="' . $que->uuid .
                        '">' . __('active') . '</button>
                    ';
                } else {
                    $data = '
<button type="button"  data-url="' . $currentUrl . "/admin/social/updateStatus/1/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-danger " data-uuid="' . $que->uuid .
                        '">' . __('inactive') . '</button>
                    ';
                }
                return $data;
            })
            ->rawColumns(['action', 'status'])->toJson();
    }

    public function updateStatus($status, $sup)
    {
        $uuids = explode(',', $sup);

        $activate = Social::query()->withoutGlobalScope('status')
            ->whereIn('uuid', $uuids)
            ->update([
                'status' => $status
            ]);
        return response()->json([
            'item_edited'
        ]);
    }


}
