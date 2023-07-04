<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServingResource extends JsonResource
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
            'working_condition' => $this->working_condition,
            'tag' => 'new',
            'details' => $this->details,
            'price' => $this->price,
            'currency' => __('sr'),
            'from' => $this->from,
            'created' => $this->created_at->diffForHumans(),

        ];
    }
}
