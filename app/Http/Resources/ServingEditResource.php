<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServingEditResource extends JsonResource
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
            'category_uuid' => $this->category->uuid,
            'category_name' => $this->category_name,
            'working_condition' => $this->working_condition,
            'city_name' => $this->city_name,
            'city_uuid' => $this->city_uuid,
            'details' => $this->details,
            'price' => $this->price,
            'currency' => __('sr'),
            'from' => $this->from,
            'to' => $this->to,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'address' => $this->address,
        ];
    }
}
