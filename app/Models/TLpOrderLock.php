<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TLpOrderLock extends Model
{
    protected $table = 't_lp_order_locks';

    protected $fillable  = [
        "id",
        "lp_order_id",
        "menu_id",
        "created_pg",
        "created_at",
        "updated_pg",
        "updated_at",
        "deleted_pg",
        "deleted_at",
    ];
}
