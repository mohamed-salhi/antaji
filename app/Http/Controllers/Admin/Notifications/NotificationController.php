<?php

namespace App\Http\Controllers\Admin\Notifications;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\City;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class NotificationController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:notification', ['only' => ['index','store','create','destroy','edit','update']]);
    }
    public function index()
    {
        $users = DB::table('users')->select('name', 'uuid')->get();
        $cities = City::query()->select('name', 'uuid')->get();

        return view('admin.notifications.index', compact('cities', 'users'));
    }

    public function store(Request $request)
    {
        $rules = [];
        foreach (locales() as $key => $language) {
            $rules['title_' . $key] = 'required|string';
            $rules['content_' . $key] = 'required|string';
        }
        $rules['notification_according_to'] = 'nullable|in:1,2';

        $this->validate($request, $rules);
        $data = [];
        foreach (locales() as $key => $language) {
            $data['title'][$key] = $request->get('title_' . $key);
            $data['content'][$key] = $request->get('content_' . $key);
        }
//                dd($request->all());
//        $data['type']=$request->type;
        if ($request->all_users||$request->all_cities) {
            $uuids = User::query()->pluck('uuid')->toArray();

            $this->sendNotification(null, Notification::class, Auth::id(), $uuids, Notification::GENERAL_NOTIFICATION, 'admin', User::USER, $data);
        } elseif ($request->has('user_id')) {
            $this->sendNotification(null, Notification::class, Auth::id(), $request->user_id, Notification::GENERAL_NOTIFICATION, 'admin', User::USER, $data);
        } elseif ($request->all_users) {
            $uuids = User::query()->pluck('uuid')->toArray();

            $this->sendNotification(null, Notification::class, Auth::id(), $uuids, Notification::GENERAL_NOTIFICATION, 'admin', User::USER, $data);
        } elseif ($request->has('city_id')) {
            $uuids = User::query()->whereIn('city_uuid', $request->city_id)->pluck('uuid')->toArray();
//           dd($uuids);
            $this->sendNotification(null, Notification::class, Auth::id(), $uuids, Notification::GENERAL_NOTIFICATION, 'admin', User::USER, $data);

        }


        return response()->json([
            'done'
        ]);
    }

    public function destroy($uuid)
    {

        try {
            $uuids = explode(',', $uuid);
            $ads = Notification::whereIn('uuid', $uuids)->delete();

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
        $notfications = Notification::query()->where('type', Notification::GENERAL_NOTIFICATION)->orderByDesc('created_at');

        return Datatables::of($notfications)
            ->addColumn('checkbox', function ($que) {
                return $que->uuid;
            })
            ->addColumn('action', function ($que) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $que->uuid . '" ';
                foreach (locales() as $key => $value) {
                    $data_attr .= 'data-title_' . $key . '="' . $que->getTranslation('title', $key) . '" ';
                    $data_attr .= 'data-content_' . $key . '="' . $que->getTranslation('content', $key) . '" ';
                }
                $string = '';
                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger btn_delete" data-uuid="' . $que->uuid .
                    '">' . __('delete') . '</button>';

                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary btn_details" data-toggle="modal"
                    data-target="#btn_details" ' . $data_attr . '>' . __('details') . '</button>';

                return $string;
            })
            ->rawColumns(['action', 'status'])->toJson();
    }


}
