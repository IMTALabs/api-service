<?php

namespace App\Http\Resources\English;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'username' => $this->full_name,
            'email' => $this->email,
            'phoneNumber' => $this->mobile,
            'about' => $this->about,
            'avatar' => $this->avatar,
            'accounting_charge' => $this->getAccountingCharge(),
            'language' => $this->language,
            'timezone' => $this->timezone,
            'created_at' => $this->created_at,
            'deleted_at' => $this->deleted_at,
            'updated_at' => $this->updated_at
        ];
    }
}
