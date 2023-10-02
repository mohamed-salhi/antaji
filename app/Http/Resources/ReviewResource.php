<?php

namespace App\Http\Resources;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $startDate = Carbon::parse(@$this->start);
        $endDate = Carbon::parse(@$this->end);
        $daysDifference = @$endDate->diffInDays(@$startDate);
        $item = [
            'uuid' => @$this->uuid,
            'name' => @$this->content->name,
            'type' => @$this->type_text,

            'image' => @$this->content->image,
            'price' => @$this->content->price,
            'currency' => __('sr'),
            'type_text' => (@$this->type != Product::RENT) ? __('sale') : __('rent') . ' , ' . $daysDifference . ' ' . __('days'),
        ];
        return $item;
    }
}
