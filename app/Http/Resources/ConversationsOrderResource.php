<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationsOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->customer == $this->user_uuid) {
            $user = 'owner';
        } else {
            $user = 'customer';
        }
        return [
            'conversation_uuid' => $this->uuid,
            'user_uuid' => $this->$user->uuid,
            'user_image' => @$this->$user->image,
            'user_name' => @$this->$user->name,
            'last' => @$this->last_msg,
            'count' => @$this->count_msg,
        ];
    }
}
