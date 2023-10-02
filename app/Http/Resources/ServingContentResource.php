<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServingContentResource extends JsonResource
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
            'working_condition' => __($this->working_condition),
            'category_name' => $this->category_name,
            'city_name' => $this->city_name,
            'price' => $this->price,
            'currency' => __('sr'),
            'from' => $this->from,
            'created' => $this->created_at->diffForHumans(),
        ];
    }
}
