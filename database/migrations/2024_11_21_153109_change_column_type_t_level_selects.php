<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnTypeTLevelSelects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_level_selects', function (Blueprint $table) {
            $table->text('point1')->change();
            $table->text('point2')->change();
            $table->text('point3')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_level_selects', function (Blueprint $table) {
            $table->string('point1', 255)->change();
            $table->string('point2', 255)->change();
            $table->string('point3', 255)->change();
        });
    }
}
