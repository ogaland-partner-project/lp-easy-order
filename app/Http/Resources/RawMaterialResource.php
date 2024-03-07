<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RawMaterialResource extends JsonResource
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
            'item_karte_id' => $this->item_karte_id,
            'id' => $this->id,
            'item_karte_id' => $this->item_karte_id,
            'raw_material_name' => $this->raw_material_name,
            'production_area' => $this->production_area,
            'raw_material_detail' => $this->raw_material_detail,
            'production_area_publish_flag' => $this->production_area_publish_flag,
            'photo_material_flag' => $this->photo_material_flag,
            'certificate_flag' => $this->certificate_flag,
            'coverage_content_flag' => $this->coverage_content_flag,
            'document_path' => $this->document_path,
            'created_pg' => $this->created_pg,
            'created_at' => $this->created_at,
            'updated_pg' => $this->updated_pg,
            'updated_at' => $this->updated_at,
            'deleted_pg' => $this->deleted_pg,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
