<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseDetailsResource extends JsonResource
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
            'name' => $this->name,
            'details' => $this->details,
            'is_purchased' => $this->is_purchased,
            'price' => $this->price,
            'currency' => __('sr'),
            'demonstration_video' => $this->video,
            'count' => $this->course_count,
//            'cover'=>$this->image,
            'videos' => $this->attachments,
        ];
        $item['owner'] = [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'image' => $this->image,
            'specialization' => $this->specialization_name,
        ];
        return $item;
    }
}
