<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProducEdittResource extends JsonResource
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
            'category_name' => $this->category_name,
            'sup_category_name' => $this->sup_category_name,
            'details' => $this->details,
            'price' => $this->price,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'specifications' => $this->specifications()->select('uuid','key','value')->get(),
            'images'=>$this->attachments,
            'currency' => __('sr')
        ];
    }
}
