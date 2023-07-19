<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'image' => $this->image,
            'content_type' => $this->content_type,
            'price' => $this->price,
            'currency' => __('sr')
        ];

        if ($request->uuid) {
            $item['type'] = $this->type;
            $item['is_favorite'] = $this->is_favorite;
            $item['attachments'] = $this->attachments;
            $item['details'] = $this->details;
            $item['specifications'] = $this->specifications()->select('uuid', 'key', 'value')->get();
            $item['lat'] = $this->lat;
            $item['lng'] = $this->lng;
            $item['owner'] = new OwnerResource($this->user);
        }

        return $item;
    }
}
