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
        $startDate = Carbon::parse($this->start);
        $endDate = Carbon::parse($this->end);
        $daysDifference = $endDate->diffInDays($startDate);
        return [
            'uuid'=>$this->uuid,
            'image'=>@$this->products->image??@$this->locations->image,
            'price'=>@$this->products->price??@$this->locations->price,
            'count'=>$daysDifference
        ];
    }
}
