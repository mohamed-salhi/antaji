<?php

namespace App\Http\Controllers\Admin\Contact;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;

class ContactController extends Controller
{
    public function index(Request $request)
    {
//        if ($request->has('uuid')){
//
//            ViewNotificationAdmin::query()->updateOrCreate([
//                'admin_id'=>Auth::id(),
//                'notification_admins_uuid'=>$request->uuid
//            ]);
//        }
        return view('admin.contacts.index');
    }

    public function destroy($uuid)
    {
//        Gate::authorize('place.delete');

        try {
            $uuids = explode(',', $uuid);
            $help = Contact::whereIn('uuid', $uuids)->get();
            foreach ($help as $item) {
                File::delete(public_path('upload/image/help/' . $item->imageContact->filename));
                $item->delete();
            }
            return response()->json([
                'item_deleted'
            ]);         } catch (\Exception $e) {
            return response()->json([
                'err'
            ]);
        }
    }

    public function indexTable(Request $request)
    {
        $contacts = Contact::query()->orderByDesc('created_at');
        return Datatables::of($contacts)
            ->filter(function ($query) use ($request) {

                if ($request->view) {
                    $query->where('view', $request->view);
                }
                if ($request->importance) {
                    $query->where('importance', $request->importance);
                }
                if ($request->name) {
                    $query->where('name', 'like', "%{$request->name}%");
                }
                if ($request->email) {
                    $query->where('email', 'like', "%{$request->email}%");
                }

            })
            ->addColumn('checkbox', function ($que) {
                return $que->uuid;
            })
            ->addColumn('action', function ($que) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $que->uuid . '" ';
                $data_attr .= 'data-name="' . $que->name . '" ';
                $data_attr .= 'data-email="' . $que->email . '" ';
                $data_attr .= 'data-view="' . $que->view . '" ';
                $data_attr .= 'data-Importance="' . $que->Importance . '" ';
                $data_attr .= 'data-description="' . $que->description . '" ';
                $data_attr .= 'data-image="' . $que->image . '" ';
//                $user = Auth()->user();

                $string = '';
                $currentUrl = url('/');
                $string .= '<button class="detail_btn btn btn-sm btn-outline-success btn_detail" data-toggle="modal"
                    data-target="#detail_modal" data-url="' . $currentUrl . "/contacts/view/" . $que->uuid . '" ' . $data_attr . '>' . __('details') . '</button>';

//                if ($user->can('help-delete')) {
                    $string .= ' <button type="button" class="btn btn-sm btn-outline-danger btn_delete" data-uuid="' . $que->uuid .
                        '">' . __('delete') . '</button>';
//                }
//                if ($user->can('help-edit')) {
                    $string .= '
                                                                <div class="btn-group">
                                                                    <button type="button" class="btn btn-sm btn-dark dropdown-toggle"
                                                                            data-toggle="dropdown">
                                                                              ' . __('status') . '

                                                                        </button>
                                                                    <div class="dropdown-menu">
                                                                        <button class="dropdown-item" data-url="' . $currentUrl . "/contacts/importance/" . $que->uuid . "/3" . '">
                                                                            <i data-feather="edit-2" class="mr-50"></i>
                                                                            <span>' . __('very important') . '</span>
                                                                        </button>
                                                                        <button class="dropdown-item" data-url="' . $currentUrl . "/contacts/importance/" . $que->uuid . "/2" . '">
                                                                            <i data-feather="edit-2" class="mr-50"></i>
                                                                            <span>' . __('important') . '</span>
                                                                        </button>
                                                                        <button class="dropdown-item" data-url="' . $currentUrl . "/contacts/importance/" . $que->uuid . "/1" . '">
                                                                            <i data-feather="edit-2" class="mr-50"></i>
                                                                            <span>' . __('normal') . '</span>
                                                                        </button>

                                                                    </div>
                                                                </div>
                                                                                                                          </div>';
//                }

                return $string;
            })
            ->rawColumns(['action'])->toJson();
    }

    public function view($uuid)
    {
        $data = Contact::find($uuid);
        $data->view = Contact::WATCHED;
        if (isset($data) && $data->save()) {
            return response()->json([
                'item_edited'
            ]);         }
    }

    public function importance($uuid, $importance)
    {
        $data = Contact::find($uuid);
        $data->importance = $importance;
        if (isset($data) && $data->save()) {
            return response()->json([
                'item_edited'
            ]);
        }
    }
}
