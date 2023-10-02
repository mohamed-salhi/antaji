<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('msg.{id}', function ($user, $id) {

//        if($user->uuid==$id||$user->id){
//            return true;
//        }
return  false;
});
Broadcast::channel('chat.{id}', function ($user, $id) {

    if ($user->hasAbility($id)){
        return true;
    }

});

Broadcast::channel('order.{id}', function ($user, $id) {

    $check= \App\Models\OrderConversation::query()->where('uuid',$id)->where(function ($q)use ($user){
        $q->where('customer_uuid',$user->uuid)->orWhere('owner_uuid',$user->uuid);
    })->exists();
    if ($check) {
        return true;
    }
});
