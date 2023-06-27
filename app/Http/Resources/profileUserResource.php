<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class profileUserResource extends JsonResource
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
            'address' => $this->address,
            'name' => $this->name,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'brief' => $this->brief,
            'video' => $this->video_user,
            'personal_photo' => $this->image,
            'cover_Photo' => $this->cover_user

        ];
    }
}
