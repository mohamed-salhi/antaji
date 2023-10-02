<?php

namespace App\Http\Controllers\Admin\Package;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PackageController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:package', ['only' => ['index','store','create','destroy','edit','update']]);
    }
    public function index()
    {
        return view('admin.packages.index');
    }

    public function update(Request $request)
    {

        $rules = [];
        foreach (locales() as $key => $language) {
            $rules['name_' . $key] = 'required|string';
            $rules['details_' . $key] = 'required|string';
        }
        $rules['percentage_of_sale'] = 'required|int';
        $rules['price'] = 'required|int';
        $rules['quality'] = 'required|in:4k,1080p';
        $rules['number_of_products_in_each_section'] = 'required|int';

        $this->validate($request, $rules);
        $data = [];
        foreach (locales() as $key => $language) {
            $data['name'][$key] = $request->get('name_' . $key);
            $data['details'][$key] = $request->get('details_' . $key);
        }
        $data['price'] = $request->price;
        $data['percentage_of_sale'] = $request->percentage_of_sale;
        $data['quality'] = $request->quality;
        $data['number_of_products_in_each_section'] = $request->number_of_products_in_each_section;
        $package = Package::query()->findOrFail($request->uuid);
        $package->update($data);
        return response()->json([
            'item_edited'
        ]);

    }

    public function indexTable(Request $request)
    {
        $packages = Package::query()->orderByDesc('created_at');

        return Datatables::of($packages)
            ->addColumn('checkbox', function ($que) {
                return $que->uuid;
            })
            ->addColumn('action', function ($que) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $que->uuid . '" ';
                $data_attr .= 'data-price="' . $que->price . '" ';
                $data_attr .= 'data-number_of_products_in_each_section="' . $que->number_of_products_in_each_section . '" ';
                $data_attr .= 'data-quality="' . $que->quality . '" ';
                $data_attr .= 'data-percentage_of_sale="' . $que->percentage_of_sale . '" ';

                $string = '';
                foreach (locales() as $key => $value) {
                    $data_attr .= 'data-name_' . $key . '="' . $que->getTranslation('name', $key) . '" ';
                    $string .= '<div hidden id="data_details_' . $que->uuid . '_' . $key . '">' . $que->getTranslation('details', $key) . '</div>';
                }
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary btn_edit" data-toggle="modal"
                    data-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';

                return $string;
            })->addColumn('status', function ($que) {
                $currentUrl = url('/');
                if ($que->status == 1) {
                    $data = '
<button type="button"  data-url="' . $currentUrl . "/admin/packages/updateStatus/0/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-success " data-uuid="' . $que->uuid .
                        '">' . __('active') . '</button>
                    ';
                } else {
                    $data = '
<button type="button"  data-url="' . $currentUrl . "/admin/packages/updateStatus/1/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-danger " data-uuid="' . $que->uuid .
                        '">' . __('inactive') . '</button>
                    ';
                }
                return $data;
            })
            ->rawColumns(['action', 'status'])->toJson();
    }

    public function UpdateStatus($status, $sub)
    {
        $uuids = explode(',', $sub);

        $activate = Package::query()
            ->whereIn('uuid', $uuids)
            ->update([
                'status' => $status
            ]);
        return response()->json([
            'item_edited'
        ]);
    }
}
