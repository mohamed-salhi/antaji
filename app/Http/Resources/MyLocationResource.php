<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MyLocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        $sub = $this->whenLoaded('categories', function () {
//            return CategoryResource::collection($this->categories);
//        });
        return[
            'uuid' => $this->uuid,
            'name' => $this->name,
            'image' => $this->image,
            'categories_name' => implode(', ', $this->categories->pluck('name')->toArray()) ,
            'price' => $this->price,
            'currency' => __('sr')
        ];


    }
}
