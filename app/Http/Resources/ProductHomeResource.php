<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductHomeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (@$this->categories){
            $sub = @$this->whenLoaded('categories', function () {
                return CategoryResource::collection(@$this->categories);
            });
        }

        $item= [
            'uuid' => @$this->uuid,
            'name' => @$this->name,
            'type' => @$this->type,
            'image' => @$this->image,
            'price' => @$this->price,
            'currency' => __('sr')
        ];
        if ($request->product){
            $item['category_name']=@$this->category_name;
            $item['sub_category_name']=@$this->sub_category_name;
        }
        if ($request->location){
            $item['category_name']=CategoriesLocation::collection(@$this->categories);
        }
        return $item;
    }
}
