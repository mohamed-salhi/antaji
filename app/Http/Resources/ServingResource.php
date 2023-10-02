<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
        $item = [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'category_name' => $this->category_name,
            'working_condition' =>__( $this->working_condition),
            'is_new' => $this->created_at->isBefore(Carbon::now()->subDays()),
            'is_special' => (fmod($this->id, 3) == 0),
            'city_name' => $this->city_name,
            'details' => $this->details,
            'price' => $this->price,
            'currency' => __('sr'),
            'created' => $this->created_at->diffForHumans(),
            'from' => $this->from,

        ];

        if ($request->uuid) {
            $item['to'] = $this->to;
            $item['owner'] = new OwnerResource($this->user);
        }

        return $item;
    }
}
