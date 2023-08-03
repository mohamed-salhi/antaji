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

class ChatOrderEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels ;


    public $msg;
    public $user_uuid;
    public $order_conversation_uuid;

    /**
     * Create a new event instance.
     */
    public function __construct($msg,$user_uuid,$order_conversation_uuid,)
    {
        $this->msg=$msg;
        $this->user_uuid=$user_uuid;
        $this->order_conversation_uuid=$order_conversation_uuid;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('order.'.$this->order_conversation_uuid),
        ];
    }
    public function broadcastAs(): string
    {
        return 'order';
    }
    public function broadcastWith(): array{
        return [$this->msg,$this->user_uuid,$this->order_conversation_uuid];
    }
}
