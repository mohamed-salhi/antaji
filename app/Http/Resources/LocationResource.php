<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $sub = $this->whenLoaded('categories', function () {
            return CategoryResource::collection($this->categories);
        });
        $item = [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'image' => $this->image,
            'price' => $this->price,
            'currency' => __('sr')
        ];
        if ($request->uuid) {
            $item['is_favorite'] = $this->is_favorite;
            $item['attachments'] = $this->attachments;
            $item['details'] = $this->details;
            $item['lat'] = $this->lat;
            $item['lng'] = $this->lng;
            $item['owner'] = new OwnerResource($this->user);
        }
        return $item;
    }
}
