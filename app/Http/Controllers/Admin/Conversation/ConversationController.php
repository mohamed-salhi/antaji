<?php

namespace App\Http\Controllers\Admin\Conversation;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConversationsResource;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ConversationController extends Controller
{
    public function index($uuid)
    {
       $user= User::query()->findOrFail($uuid);
        $items = Conversation::query()
            ->where('one', $uuid)
            ->orWhere('tow', $uuid)
            ->has('chat')
            ->get();

        $req = new Request();
        $req->request->add(['user_uuid', $uuid]);
//      return  $items = ConversationsResource::collection($items2);
        $conversation = Conversation::query()
            ->where(function ($q) use ($uuid) {
                $q->where('one', $uuid);
            })->orWhere(function ($q) use ($uuid) {
                $q->where('tow', $uuid);
            })
            ->first();
        $uuid_user = $uuid;
        $user_name=$user->name;
        return view('admin.conversations.index', compact('items', 'uuid_user', 'conversation','user_name'));
    }


    public function chat($uuid)
    {
//        $users = User::query()->select('uuid','name')->has('message')->with('message')->withCount([
//            'message'=> function($q){
//                $q->whereNull('view_admin')->where('status','user');
//            }
//
//        ])->orderByDesc('created_at')->get();
//            Message::query()->where('user_uuid',$uuid)->whereNull('view_admin')->where('status','user')->update([
//                'view_admin'=>date('Y-m-d H:i:s')
//            ]);


        $conversation = Conversation::query()->orderByDesc('created_at')->findOrFail($uuid);
        $uuid_user = $conversation->user->uuid;

//            $chat = User::query()->where('uuid', $uuid)->with([
//                'message'=> function($q){
//                    $q->paginate(20);
//                }
//            ])->orderBy('created_at')->first();
//            $seen=false;
//            $check= Message::query()->where('user_uuid',$uuid)->latest()->first();
//            if ( $check->view_user){
//                $seen=true;
//
//            }
        return view('admin.conversations.chat', compact('conversation', 'uuid_user'))->render();


    }

    public function details($uuid)
    {
        $conversation = Conversation::query()->findOrFail($uuid);
        return $chat = $conversation->chat;
    }
}
