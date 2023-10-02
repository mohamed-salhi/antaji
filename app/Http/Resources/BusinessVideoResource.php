<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessVideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'image'=>$this->image,
            'title'=>$this->title,
            'video'=>$this->video,
            'artist_uuid'=>@$this->artists->uuid,
            'artist_name'=>@$this->artist_name,
            'artist_image'=>@$this->artist_image

        ];
    }
}
