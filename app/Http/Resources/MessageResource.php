<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'is_me' => $this->status == 'user',
            'time' => Carbon::parse($this->created_at)->format('h:m A'),
            'type' => $this->type,
            'type_text' => $this->type_text,
            'content' => $this->content,
        ];
    }
}
