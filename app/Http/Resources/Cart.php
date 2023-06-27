<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Cart extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'user_uuid'=>$this->content_owner_uuid,
            'user_name'=>$this->content_owner_name,
            'count'=>$this->content_count,
            'countent'=>new CartContent($this),
            'commission'=>$this->commission,
            'price_with_day'=>$this->price_with_day,
            'multi_day_discounts'=>$this->multi_day_discounts,
            'all'=>$this->commission+$this->price_with_day+$this->multi_day_discounts
        ];
    }
}
