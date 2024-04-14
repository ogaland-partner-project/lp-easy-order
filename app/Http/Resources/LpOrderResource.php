<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LpOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $locks = $this->tLpOrderLocks->isEmpty() ? '' : '編集中';
        return [
            'id' => $this->id,
            'product_name' => $this->product_name,
            'product_code' => $this->product_code,
            'description' => $this->description,
            'lock' => $locks,
            'status' => $this->status,
            'requirement_flag' => $this->requirement_flag,
            'promoter' => $this->tLevelSelects[0]->promoter,
            'configurator' => $this->tLevelSelects[0]->configurator,
            'designer' => $this->tLevelSelects[0]->designer,
            'rightmenu' => 0,
        ];
    }
}
