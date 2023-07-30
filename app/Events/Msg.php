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
    public $created_at;
    /**
     * Create a new event instance.
     */
    public function __construct($content,$name,$status,$uuid,$image,$type,$created_at)
    {
        $this->content=$content;
        $this->name=$name;
        $this->status=$status;
        $this->uuid=$uuid;
        $this->image=$image;
        $this->type=$type;
        $this->created_at=$created_at;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('msg.'.$this->uuid),
        ];
    }
    public function broadcastAs(): string
    {
        return 'msg';
    }
    public function broadcastWith(): array{
        return [$this->content,$this->name,$this->status,$this->uuid,$this->image,$this->type,$this->created_at];
    }
}
