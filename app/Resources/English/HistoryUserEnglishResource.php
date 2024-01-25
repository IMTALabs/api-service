<?php

namespace App\Http\Resources\English;

use Illuminate\Http\Resources\Json\JsonResource;

class HistoryUserEnglishResource extends JsonResource
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
            'skill' => $this->skill::DISPLAY_NAME,
            'created_at' => $this->created_at->locale('en_US')->diffForHumans(),
        ];
    }
}
