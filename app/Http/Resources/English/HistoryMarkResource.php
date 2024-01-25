<?php

namespace App\Http\Resources\English;

use Illuminate\Http\Resources\Json\JsonResource;

class HistoryMarkResource extends JsonResource
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
            'skill'=>$this->skill::DISPLAY_NAME,
            'mark'=> json_decode($this->mark),
            'score'=>$this->score,
            'created'=>$this->created_at,
        ];
    }
}
