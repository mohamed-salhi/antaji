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
            'uuid' => $this->uuid,
            'name' => $this->serving->name,
            'working_condition' => $this->serving->working_condition,
//            'is_new' => $this->created_at->isBefore(Carbon::now()->subDays()),
//            'is_special' => (fmod($this->id, 3) == 0),
            'city_name' => $this->serving->city_name,
            'category_name'=>$this->serving->category_name,

            'price' => $this->serving->price,
            'currency' => __('sr'),
            'created' => $this->serving->created_at->diffForHumans(),
            'status' => $this->status,
        ];



        return $item;
    }
}
