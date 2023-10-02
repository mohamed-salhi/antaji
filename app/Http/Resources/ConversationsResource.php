<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->user_uuid == $this->one) {
            $user = 'userTow';
        } else {
            $user = 'userOne';
        }
        return [
            'conversation_uuid' => $this->uuid,

            'user_uuid' => $this->$user->uuid,
            'user_image' => @$this->$user->image,
            'user_name' => @$this->$user->name,
            'last' => @$this->last_msg,
            'count' => @$this->count_msg,
//            'created' =>  $this->chat()->latest()->first()->value('created_at')->diffForHumans(),

        ];
    }
}
