<?php

namespace App\Http\Resources\English;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'username' => $this->name,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'accounting_charge' => $this->balance,
        ];
    }
}
