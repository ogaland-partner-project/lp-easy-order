<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LevelSelectResource extends JsonResource
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
            'lp_order_id' => $this->lp_order_id,
            'promoter' => $this->promoter,
            'configurator' => $this->configurator,
            'designer' => $this->designer,
            'level' => $this->level,
            'purpose' => $this->purpose,
            'point1' => $this->point1,
            'point2' => $this->point2,
            'point3' => $this->point3,
            'taste' => $this->taste,
            't_level_select_lp_blocks' => LevelSelectLpBlockResource::collection($this->tLevelSelectLpBlocks),
        ];
    }
}
