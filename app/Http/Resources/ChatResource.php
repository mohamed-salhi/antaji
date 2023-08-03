<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type_text' => $this->type_text,
            'user_uuid' => $this->user_uuid,
            'content' => $this->content,
            'created_at' => $this->created_at->format('h:m A'),
//            'created_at' => $this->created_at->isToday() ? $this->created_at->format('h:m A') : $this->created_at->format('Y-m-d'),


        ];
    }
}
