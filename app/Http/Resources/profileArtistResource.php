<?php

namespace App\Http\Resources;

use App\Models\Reviews;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class profileArtistResource extends JsonResource
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
            'type' => $this->type,
            'is_verified' => false,
            'is_favorite' => false,
            'name' => $this->name,
            'skills' => ($this->type == 'artist') ? $this->skills()->select('name')->get() : null,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'brief' => $this->brief,
            'specialization' => ($this->type == 'artist') ? $this->specialization_name : null,
            'video' => $this->video_user,
            'personal_photo' => $this->image,
            'cover_Photo' => $this->cover_user,
            'created_at' => Carbon::parse($this->created_at)->format('F,Y'),
            'reviews_count' => $this->reviews,
            'response' => $this->response,
            'reviews' => Reviews::query()->where('reference_uuid', $this->uuid)->take(5)->get()

        ];
    }
}
