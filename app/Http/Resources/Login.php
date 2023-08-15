<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Login extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'token' => $this->token,
            'name' => $this->name,
            'image' => $this->image,
            'type' => $this->type,
            'specialization_name' => $this->specialization_name,
        ];
    }
}
