<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConstitutionProcessResource extends JsonResource
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
            'concept_word' => $this->concept_word,
            'concept_catch' => $this->concept_catch,
            'how_block' => $this->how_block,
            'constitution_blocks_list' => new ConstitutionProcessBlockResource($this['tConstitutionBlocks'][0]),
            'constitution_fix_blocks_list' => new ConstitutionProcessFixBlockResource($this['tConstitutionFixBlocks'][0]),
        ];
    }
}
