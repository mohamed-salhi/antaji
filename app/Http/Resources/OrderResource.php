<?php

namespace App\Http\Resources;

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
        return [
            'uuid'=>$this->uuid,
            'name'=>$this->content->name,
            'price'=>@$this->content->price,
            'status'=>$this->status,
            'image'=>@$this->content->image,
            'count'=>@$daysDifference
        ];
    }
}
