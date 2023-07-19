<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class CartContent extends JsonResource
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
            'name' => @$this->products->name ?? @$this->locations->name,
            'image' => @$this->products->image ?? @$this->locations->image,
            'price' => @$this->products->price ?? @$this->locations->price,
            'currency' => __('sr'),
            'days_count' => $this->days_count
        ];
    }
}
