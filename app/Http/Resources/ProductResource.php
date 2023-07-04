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
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'category_name' => $this->category_name,
            'sup_category_name' => $this->sup_category_name,
            'image' => $this->image,
            'price' => $this->price,
            'currency' => __('sr')
        ];
    }
}
