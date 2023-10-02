<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseTrakingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => @$this->uuid,
            'name' => @$this->name,
            'type_text' => __(Order::COURSE),
            'price' => @$this->price,
            'currency' => __('sr'),
            'cover' => @$this->image,
        ];
    }
}
