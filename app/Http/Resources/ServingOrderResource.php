<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServingOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $item = [
            'uuid' => @$this->uuid,
            'name' => @$this->service->name,
            'working_condition' => @$this->service->working_condition,
            'city_name' => @$this->service->city_name,
            'category_name' => @$this->service->category_name,
            'price' => @$this->service->price,
            'currency' => __('sr'),
            'started_at' => Carbon::parse(@$this->start)->format('d/m/Y'),
            'status_text' => @$this->status_text,
            'status_color' => @$this->color,
            'status_bg_color' => @$this->status_bg_color,

        ];


        return $item;
    }
}
