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
        $item = [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'working_condition' => $this->working_condition,
//            'is_new' => $this->created_at->isBefore(Carbon::now()->subDays()),
//            'is_special' => (fmod($this->id, 3) == 0),
              'category_name'=>$this->category_name,
            'city_name' => $this->city_name,
            'price' => $this->price,
            'currency' => __('sr'),
            'created' => $this->created_at->diffForHumans(),
            'status' => $this->status,

        ];



        return $item;
    }
}
