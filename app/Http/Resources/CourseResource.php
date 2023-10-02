<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'is_purchased' => $this->is_purchased,
            'price' => $this->price,
            'currency' => __('sr'),
            'cover' => $this->image,
            'count' => $this->course_count,
            'video' => $this->video,
        ];
    }
}
