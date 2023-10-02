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

class Chat implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $msg;
    public $user_uuid;
    public $conversation_uuid;

    /**
     * Create a new event instance.
     */
    public function __construct($msg, $user_uuid, $conversation_uuid,)
    {
        $this->msg = $msg;
        $this->user_uuid = $user_uuid;
        $this->conversation_uuid = $conversation_uuid;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('chat.' . $this->conversation_uuid),
        ];
    }

    public function broadcastAs(): string
    {
        return 'chat';
    }

    public function broadcastWith(): array
    {
        return [
            'content' => $this->msg,
            'user_uuid' => $this->user_uuid,
            'conversation_uuid' => $this->conversation_uuid];
    }
}
