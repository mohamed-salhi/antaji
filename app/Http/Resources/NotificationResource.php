<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
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
            'icon' => $this->icon,
            'created_at' => $this->created_at->diffForHumans(),
            'title' => $this->title,
            'content' => $this->content,
            'type' => $this->type,
            'reference_uuid' => $this->reference_uuid,
            'reference_type' => $this->reference_type,
        ];
    }
}
