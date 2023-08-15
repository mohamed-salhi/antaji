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
            'name'=>$this->name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'mobile_without_country_code' => @explode( '-', $this->mobile)[1],
            'mobile_country_code' => @explode( '-', $this->mobile)[0],
            'country_image' => @$this->country->image,
            'country_name' => $this->country_name,
            'country_uuid' => $this->country_uuid,
            'city_name' => $this->city_name,
            'city_uuid' => $this->city_uuid,
            'is_id_verified' => ($this->documentation)?true:false,
        ];
    }
}
