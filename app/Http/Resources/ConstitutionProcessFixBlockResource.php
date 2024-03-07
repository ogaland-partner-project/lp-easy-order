<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConstitutionProcessFixBlockResource extends JsonResource
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
            'constitution_process_id' => $this->constitution_process_id,
            'block_detail' => $this->block_detail,
            'sort_order' => $this->sort_order,
        ];
    }
}
