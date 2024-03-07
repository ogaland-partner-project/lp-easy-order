<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LevelSelectLpBlockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'level_select_id' => $this->level_select_id,
            'block_detail' => $this->block_detail,
            'sort_order' => $this->sort_order,
        ];
    }
}
