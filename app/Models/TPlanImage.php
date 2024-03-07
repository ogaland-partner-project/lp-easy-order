<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TPlanImage extends Model
{
    protected $table = 't_plan_images';
    //
    use SoftDeletes;
    protected $fillable  = [
        "id",
        "constitution_plan_id",
        "image_path",
        "sort_order",
        "created_pg",
        "created_at",
        "updated_pg",
        "updated_at",
        "deleted_pg",
        "deleted_at"
    ];


    public function constitutionPlan()
    {
        return $this->belongsTo(TConstitutionPlan::class, 'constitution_plan_id');
    }
}
