<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $sub = $this->whenLoaded('sub', function () {
            return CategoryResource::collection($this->sub);
        });
        return [
            'uuid' => @$this->uuid,
            'name_translate' => @$this->name_translate,
            'sub_categories' => @$sub,
        ];
    }
}
