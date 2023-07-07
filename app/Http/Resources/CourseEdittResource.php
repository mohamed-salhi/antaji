<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseEdittResource extends JsonResource
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
            'name' => $this->name,
            'details' => $this->details,
            'price' => $this->price,
            'demonstration_video' => $this->video,
            'cover'=>$this->image,
            'videos'=>$this->attachments,
            'currency' => __('sr')
        ];
    }
}
