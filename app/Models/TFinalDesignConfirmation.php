<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use database\factories\TFinalDesignConfirmationFactory;

class TFinalDesignConfirmation extends Model
{
    use SoftDeletes;

    protected $table = 't_final_design_confirmations';
    protected $guarded = [
        'id'
    ];
}
