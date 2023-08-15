<?php

namespace App\Http\Resources;

use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class artists extends JsonResource
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
            'specialization' => $this->specialization_name,
            'name' => $this->name,
            'personal_photo' => $this->image,
            'cover_photo' => $this->cover_user,
            'video' => $this->video_user,
            'is_verified' => $this->is_verified,
            'is_favorite' => $this->is_favorite
        ];
    }
}
