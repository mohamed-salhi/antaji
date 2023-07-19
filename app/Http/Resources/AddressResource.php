<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
            'title' => $this->title,
            'is_default' => $this->default == 1,
            'country_name' => $this->country_name,
            'city_name' => $this->city_name
        ];

        if ($request->uuid){
            $item['country_uuid'] = $this->country_uuid;
            $item['city_uuid'] = $this->city_uuid;
            $item['lat'] = $this->lat;
            $item['lng'] = $this->lng;
            $item['address'] = $this->address;
        }

        return $item;
    }
}
