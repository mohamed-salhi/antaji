<?php

namespace App\Http\Resources;

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
            'cover_Photo' => $this->cover_user
        ];
    }
}
