<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MyProductResource extends JsonResource
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
            'image' => $this->image,
            'content_type' => $this->content_type,
            'category_name' => $this->category_name,
            'sub_category_name' => $this->sub_category_name,
            'price' => $this->price,
            'currency' => __('sr')
        ];
    }
}
