<?php

namespace App\Events;

use App\Models\NotificationAdmin;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class NotificationAdminEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $type;
    public $title;
    public $content;
    public $link;

    public function __construct($type, $content, $title,$link)
    {
        $this->type = $type;
        $this->content = $content;
        $this->title = $title;
        $this->link = $link;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('NotificationAdmin.'.$this->type),
        ];
    }
    public function broadcastAs(): string
    {
        return 'NotificationAdmin';
    }
    public function broadcastWith(): array{
        return [$this->type,$this->title,$this->content,$this->link];
    }
}
