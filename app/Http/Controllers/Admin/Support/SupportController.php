<?php

namespace App\Http\Controllers\Admin\Support;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:support', ['only' => ['index','store','create','destroy','edit','update']]);
    }
    public function index($uuid = null)
    {
        $users = User::query()->select('uuid','name')->has('message')->with('message')->withCount([
            'message'=> function($q){
                $q->whereNull('view_admin')->where('status','user');
            }

        ])->orderByDesc('created_at')->get();
        if ($uuid) {
            Message::query()->where('user_uuid',$uuid)->whereNull('view_admin')->where('status','user')->update([
                'view_admin'=>date('Y-m-d H:i:s')
            ]);
            $chat = User::query()->where('uuid', $uuid)->with([
                'message'=> function($q){
                    $q->paginate(20);
                }
            ])->orderBy('created_at')->first();
            $seen=false;
            $check= Message::query()->where('user_uuid',$uuid)->latest()->first();
            if ( $check->view_user){
                $seen=true;

            }
            return view('admin.support.chat', compact('chat','seen'))->render();

        } else {
            $msg = User::query()->has('message')->with([
                'message'=> function($q){
                    $q->paginate(20);
                }
            ])->orderBy('created_at')->first();
            Message::query()->where('user_uuid',@$msg->uuid)->whereNull('view_admin')->where('status','user')->update([
                'view_admin'=>date('Y-m-d H:i:s')
            ]);
            $check= Message::query()->where('user_uuid',@$msg->uuid)->latest()->first();
            $seen=false;
            if (@$check->view_user){
                $seen=true;
            }
            return view('admin.support.index', compact('users', 'msg','seen'));

        }
    }

    public function message(Request $request)
    {
        $rules = [
            'message' => 'required|max:100',
            'user_uuid' => 'required|',
        ];
        $this->validate($request, $rules);

        $request->merge([
            'status' => 'admin',
            'type' => 1,
            'view_admin'=>date('Y-m-d H:i:s')

        ]);
        $msg = Message::create($request->only('message', 'user_uuid', 'status', 'type'));
        event(new \App\Events\Msg($request->message, $request->user_uuid, "admin", $request->user_uuid, $msg->user->image, 1, $msg->created_at,$msg->type_text));
//        event(new \App\Events\Chat($request->message, $request->user_uuid,  $request->user_uuid));
        return 'done';

    }

    public function readMore(Request $request, $uuid)
    {

        $chat = User::query()->where('uuid', $uuid)->with([
            'message'=> function($q){
            $q->paginate(5);
            }
        ])->orderBy('created_at')->first();
dd($chat);
        $data = '';
        for ($i = count($chat->message) - 1; $i > 0; $i--) {
            if ($chat->message[$i]->status == 'admin') {
                if ($chat->message[$i]->type == \App\Models\Message::TEXT) {
                    $data .= '    <li class="clearfix">
                                <div class="message-data">
                                    <span class="message-data-time"> '.$chat->message[$i]->created_at->diffForHumans().'</span>
                                </div>
                                <div class="message my-message"> '.$chat->message[$i]->content.'</div>
                            </li>';
                } elseif ($chat->message[$i]->type == \App\Models\Message::IMAGE) {
                    $data .= '              <img id="flag"
                                 src="'.$chat->message[$i]->content.'"
                                 alt=""/>';
                } elseif ($chat->message[$i]->type == \App\Models\Message::VOICE) {
                    $data .= ' <audio controls>
                                <source src="'.$chat->message[$i]->content.'" type="audio/ogg">
                                <source src="'.$chat->message[$i]->content.'" type="audio/mpeg">
                    Your browser does not support the audio element.
                            </audio>';
                }
            } else {
                if ($chat->message[$i]->type == \App\Models\Message::TEXT) {
                    $data .= ' <li class="clearfix">
                            <div class="message-data text-right">
                                <span class="message-data-time">'.$chat->message[$i]->created_at->diffForHumans().'</span>
                                <img
                                    src="'.$chat->image.'"
                                    alt="avatar">
                            </div>
                            <div class="message-data text-right">


                                    <div class="message other-message float-right">
                                        '.$chat->message[$i]->message.'
                                    </div>
                                           </div>
                        </li>
';
                }elseif ($chat->message[$i]->type == \App\Models\Message::IMAGE){
                    $data .= ' <li class="clearfix">
                            <div class="message-data text-right">
                                <span class="message-data-time">'.$chat->message[$i]->created_at->diffForHumans().'</span>
                                <img
                                    src="'.$chat->image.'"
                                    alt="avatar">
                            </div>
                            <div class="message-data text-right">


                                    <div class="message other-message float-right">
   <img
                                        src = "'.$chat->message[$i]->content.'"
                                        height = "100" width = "200" >                                    </div>
                                           </div>
                        </li>
';
                }



                    elseif($chat->message[$i]->type == \App\Models\Message::VOICE){
                        $data .= ' <li class="clearfix">
                            <div class="message-data text-right">
                                <span class="message-data-time">'.$chat->message[$i]->created_at->diffForHumans().'</span>
                                <img
                                    src="'.$chat->image.'"
                                    alt="avatar">
                            </div>
                            <div class="message-data text-right">


                                    <div class="message other-message float-right">
         <audio controls>
                                        <source src = "'.$chat->message[$i]->content.'" type = "audio/ogg" >
                                        <source src = "'.$chat->message[$i]->content.'" type = "audio/mpeg" >
                    Your browser does not support the audio element .
                                    </audio >                               </div>
                                           </div>
                        </li>
';
                    }

            }


        }

        return $data;
    }
}
