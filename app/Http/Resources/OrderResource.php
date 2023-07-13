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
        $item= [
            'uuid'=>$this->uuid,
            'name'=>$this->content->name,
            'price'=>@$this->content->price,

            'image'=>@$this->content->image,
            'count'=>@$daysDifference
        ];

        if ($request->has('owner')){
            $item['status']=($this->status==Order::PENDING)?__('new'):$this->status;
        }else{
            $item['status']=$this->status;

        }

        return $item;
    }
}
