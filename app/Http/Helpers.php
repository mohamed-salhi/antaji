<?php

use App\Http\Resources\artists;
use App\Models\Category;
use App\Models\Image;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;


function rtl_assets()
{
    if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl') {
        return '-rtl';
    }
    return '';
}

function locale()
{
    return Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale();
}

function locales()
{
    $arr = [];
    foreach (LaravelLocalization::getSupportedLocales() as $key => $value) {
        $arr[$key] = __('' . $value['name']);
    }
    return $arr;
}

function languages()
{
    if (app()->getLocale() == 'en') {
        return ['ar' => 'arabic', 'en' => 'english'];
    } else {
        return ['ar' => 'العربية', 'en' => 'النجليزية'];
    }
}


function mainResponse($status, $msg, $items, $validator = [], $code = 200, $pages = null)
{
    $item_with_paginate = $items;
    if (gettype($items) == 'array') {
        if (count($items)) {
            $item_with_paginate = $items[array_key_first($items)];
        }
    }

    if (isset(json_decode(json_encode($item_with_paginate, true), true)['data'])) {
        $pagination = json_decode(json_encode($item_with_paginate, true), true);
        $new_items = $pagination['data'];
        $pages = [
            "current_page" => $pagination['current_page'],
            "first_page_url" => $pagination['first_page_url'],
            "from" => $pagination['from'],
            "last_page" => $pagination['last_page'],
            "last_page_url" => $pagination['last_page_url'],
            "next_page_url" => $pagination['next_page_url'],
            "path" => $pagination['path'],
            "per_page" => $pagination['per_page'],
            "prev_page_url" => $pagination['prev_page_url'],
            "to" => $pagination['to'],
            "total" => $pagination['total'],
        ];
    } else {
        $pages = [
            "current_page" => 0,
            "first_page_url" => '',
            "from" => 0,
            "last_page" => 0,
            "last_page_url" => '',
            "next_page_url" => null,
            "path" => '',
            "per_page" => 0,
            "prev_page_url" => null,
            "to" => 0,
            "total" => 0,
        ];
    }

    if (gettype($items) == 'array') {
        if (count($items)) {
            $new_items = [];
            foreach ($items as $key => $item) {
                if (isset(json_decode(json_encode($item, true), true)['data'])) {
                    $pagination = json_decode(json_encode($item, true), true);
                    $new_items[$key] = $pagination['data'];
                } else {
                    $new_items[$key] = $item;
                }

                $items = $new_items;
            }
        }
    } else {
        if (isset(json_decode(json_encode($item_with_paginate, true), true)['data'])) {
            $pagination = json_decode(json_encode($item_with_paginate, true), true);
            $items = $pagination['data'];
        }
    }

//    $items = $new_items;

    $aryErrors = [];
    foreach ($validator as $key => $value) {
        $aryErrors[] = ['field_name' => $key, 'messages' => $value];
    }
    /*    $aryErrors = array_map(function ($i) {
            return $i[0];
        }, $validator);*/

    $newData = ['status' => $status, 'message' => __($msg), 'data' => $items, 'pages' => $pages, 'errors' => $aryErrors];

    return response()->json($newData);
}

function paginate($items, $perPage = 15, $page = null, $options = [])
{
    $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
    $items = $items instanceof Collection ? $items : Collection::make($items);
    return new LengthAwarePaginator($items->forPage($page, $perPage)->values(), $items->count(), $perPage, $page, $options);
}
function paginateOrder($items, $perPage = 15, $page = null, $options = [])
{
    $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
    $items = $items instanceof Collection ? $items : Collection::make($items);
    return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
}

function pageResource($data, $resource)
{
    $items = $data->getCollection();
    $items = $resource::collection($items);
    $data->setCollection(collect($items));
    return $data;
}

function latLngFormat($value)
{
    return number_format($value, 6, '.', '');
}

function UploadImage($file, $path = null, $model, $imageable_id, $update = false, $id = null, $type, $name = null)
{

    $imagename = uniqid() . '.' . $file->getClientOriginalExtension();
    $file->move(public_path($path), $imagename);
    if (!$update) {
        return Upload::create([
            'filename' => $imagename,
            'imageable_id' => $imageable_id,
            'imageable_type' => $model,
            'type' => $type,
            'name' => $name
        ]);
    } else {
        if ($name) {
            $image = Upload::where('imageable_id', $imageable_id)->where('imageable_type', $model)->where('name', $name)->first();
            if ($image) {
                File::delete(public_path($path . $image->filename));
                $image->update(
                    [
                        'filename' => $imagename,
                        'imageable_id' => $imageable_id,
                        'imageable_type' => $model,
                        'type' => $type,
                        'name' => $name
                    ]
                );
            } else {
                Upload::create([
                    'filename' => $imagename,
                    'imageable_id' => $imageable_id,
                    'imageable_type' => $model,
                    'type' => $type,
                    'name' => $name
                ]);
            }
        } else {
            $image = Upload::where('imageable_id', $imageable_id)->where('imageable_type', $model)->first();
            if ($id) {
                $image = Upload::where('uuid', $id)->first();
            }
            if ($image) {
                File::delete(public_path($path . $image->filename));
                $image->update(
                    [
                        'filename' => $imagename,
                        'imageable_id' => $imageable_id,
                        'imageable_type' => $model,
                        'type' => $type,
                        'name' => $name
                    ]
                );
            } else {
                Upload::create([
                    'filename' => $imagename,
                    'imageable_id' => $imageable_id,
                    'imageable_type' => $model,
                    'type' => $type,
                    'name' => $name
                ]);
            }
        }
    }

}

function sendFCM($message, $id, $type)
{


    $url = 'https://fcm.googleapis.com/fcm/send';


    if ($type == "android") {
        $fields = array(
            'registration_ids' => array(
                $id
            ),
            'data' => array(
                "message" => $message
            )
        );
    } else {
        $fields = array(
            'registration_ids' => array(
                $id
            ),
            'notification' => array(
                "message" => $message
            )
        );
    }
    $fields = json_encode($fields);

    $headers = array(
        'Authorization: key=' . "AAAAaVSd16w:APA91bF4SEMqymDzlNofEPXGmP9Rn8sQdSWPe1lJhgTdYGurHkCUEtiNfgxE_WQ8JcHednEYpCkCOF1LOASf6PHiFcVyitdLUiSurZHQaB5qP0UR-BfscI_OuKqiLKeA3NRx3g5D93ih",
        'Content-Type: application/json'
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

    $result = curl_exec($ch);
//    echo $result;
    curl_close($ch);
}

?>
