<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationEdittResource extends JsonResource
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
            'categories'=>CategoriesLocation::collection($this->categories()->select('uuid','name','type')->get()),
            'lat' => $this->lat,
            'lng' => $this->lng,
            'images'=>$this->attachments,
            'currency' => __('sr')
        ];
    }
}
