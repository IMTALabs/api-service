<?php

namespace App\Http\Resources\English;

use Illuminate\Http\Resources\Json\JsonResource;

class WritingRequestResource extends JsonResource
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
            'topic' => $this->topic,
            'hash' => $this->hash,
            'response' => json_decode($this->response, true),
        ];
    }
}
