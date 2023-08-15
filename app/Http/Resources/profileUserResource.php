<?php

namespace App\Http\Resources;

use App\Models\FavoriteUser;
use App\Models\Package;
use App\Models\Reviews;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

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
            'type' => $this->type,
            'is_verified' => $this->is_verified,
            'is_favorite' => $this->is_favorite,
            'name' => $this->name,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'brief' => $this->brief,
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
