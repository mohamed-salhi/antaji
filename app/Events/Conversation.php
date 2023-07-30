<?php

namespace App\Events;

use App\Models\Admin;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Conversation implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels ;


    public $conversation;

    /**
     * Create a new event instance.
     */
    public function __construct($conversation)
    {
        $this->conversation=$conversation;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('conversations.'.auth('sanctum')->id()),
        ];
    }
    public function broadcastAs(): string
    {
        return 'chat';
    }
    public function broadcastWith(): array{

$array=[];
        foreach ($this->conversation as $item){
            $data=[
                'conversation_uuid' => $item->uuid,
                'user_uuid' => $item->user->uuid,
                'user_image' => $item->user->image,
                'user_name' => $item->user->name,
                'last' => $item->last_msg,
                'count' => $item->count_msg,
                'created' =>  $item->chat()->latest()->first()->value('created_at')->diffForHumans(),
            ];
            array_push($array,$data);
        }
        return $array;
//        return [
//            [
//                'uuid' => 1,
//                'name' => 'Mohammed',
//                'image' => 'https:',
//                'unseen_messages_count' => 0,
//            ],
//            [
//                'uuid' => 2,
//                'name' => 'Mohammed',
//                'image' => 'https:',
//                'unseen_messages_count' => 0,
//            ],
//            [
//                'uuid' => 3,
//                'name' => 'Mohammed',
//                'image' => 'https:',
//                'unseen_messages_count' => 0,
//            ],
//        ];
    }
}
