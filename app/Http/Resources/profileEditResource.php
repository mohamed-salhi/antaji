<?php

namespace App\Http\Resources;

use App\Models\Package;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class profileEditResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $item = [
            'uuid' => $this->uuid,
            'type' => $this->type,
            'address' => $this->address,
            'name' => $this->name,
            'is_verified' => @$this->is_verified,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'brief' => $this->brief,
            'video' => $this->video_user,
            'personal_photo' => $this->image,
            'cover_photo' => $this->cover_user,
        ];

        if ($this->type == User::ARTIST) {
            $item['specialization_uuid'] = $this->specialization_uuid;
            $item['specialization'] = $this->specialization_name;
            $item['skills'] = $this->skills()->select('name', 'uuid')->get();
        }

        return $item;
    }
}
