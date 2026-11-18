<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'fecha' => $this->fecha?->format('Y-m-d'),
            'numero' => $this->numero,
            'created_at' => $this->created_at,
        ];
    }
}
