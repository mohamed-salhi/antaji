<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductEditResource extends JsonResource
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
            'name' => $this->name,
            'price' => $this->price,
            'category_uuid' => $this->category_uuid,
            'category_name' => $this->category_name,
            'sub_category_uuid' => $this->sub_category_uuid,
            'sub_category_name' => $this->sub_category_name,
            'details' => $this->details,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'specifications' => $this->specifications()->select('uuid', 'key', 'value')->get(),
            'images' => $this->attachments,
            'currency' => __('sr')
        ];
    }
}
