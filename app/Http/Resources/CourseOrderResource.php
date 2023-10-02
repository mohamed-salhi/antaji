<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => @$this->uuid,
            'name' => @$this->course->name,
            'price' => @$this->course->price,
            'currency' => __('sr'),
            'cover' => @$this->course->image,
            'status_text' => @$this->status_text
        ];
    }
}
