<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FinalDesignConfirmationResource extends JsonResource
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
            'design_parts' => [
                [
                    'id' => $this->id,
                    'lp_order_id' => $this->lp_order_id,
                    'image_path' => $this->image_path,
                    'file' => '',
                    'design_memo' => $this->design_memo,
                    'law_support_memo' => $this->law_support_memo,
                    'info_manage_memo' => $this->info_manage_memo,
                    'sort_order' => $this->sort_order,
                ]
            ]
        ];
    }
}
