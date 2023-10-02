<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Msg implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $content;
    public $name;
    public $status;
    public $uuid;
    public $image;
    public $type;
    public $type_text;

    public $created_at;

    /**
     * Create a new event instance.
     */
    public function __construct($content, $name, $status, $uuid, $image, $type, $created_at, $type_text)
    {
        $this->content = $content;
        $this->name = $name;
        $this->status = $status;
        $this->uuid = $uuid;
        $this->image = $image;
        $this->type = $type;
        $this->type_text = $type_text;
        $this->created_at = $created_at;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('msg.' . $this->uuid),
        ];
    }

    public function broadcastAs(): string
    {
        return 'msg';
    }

    /*
     *
     *
     * "uuid": "3935bb15-13e0-466a-a709-5ba6b085919f",
                "is_me": true,
                "time": "10:09 AM",
                "type": 1,
                "type_text": "text",
                "content": "aaaaaaaaaaaaaaaa"
     */
    public function broadcastWith(): array
    {
        return [
            'uuid' => null,
            'is_me' => ($this->status == 'user') ? true : false,
            'created_at' => $this->created_at->diffForHumans(),
            "type" => $this->type,
            "type_text" => $this->type_text,
            "content" => $this->content,
            "user_name" => $this->name,
            "user_image" => $this->image,
            "user_uuid" => $this->uuid

        ];

//            $this->content,$this->name,$this->status,$this->uuid,$this->image,$this->type,$this->created_at];
    }
}
