<?php

namespace App\Http\Resources;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $startDate = Carbon::parse(@$this->start);
        $endDate = Carbon::parse(@$this->end);
        $daysDifference = @$endDate->diffInDays(@$startDate);
        $item = [
            'uuid' => @$this->uuid,
            'name' => @$this->content->name,
            'image' => @$this->content->image,
            'price' => @$this->content->price,
            'currency' => __('sr'),
            'type_text' => @$this->type ? __('sale') : __('rent') . ' , ' . $daysDifference . ' ' . __('days'),
        ];

        $item['status_text'] = @$this->status_text;
        $item['status_color'] = @$this->status_color;
        $item['status_bg_color']= @$this->status_bg_color;

        return $item;
    }
}
