<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class acountSetting extends JsonResource
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
            'phone' => $this->mobile,
            'email' => $this->email,
            'country_name' => $this->country_name,
            'city_name' => $this->city_name,
            'name'=>$this->name,
            'countries'=>$this->country,
        ];
    }
}
