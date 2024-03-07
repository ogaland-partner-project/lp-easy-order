<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeConstitutionPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_constitution_plans', function (Blueprint $table) {
            $table->text('block_detail')->change();
            $table->text('requester_fix')->change();
            $table->text('pharmaceutical_affairs_fix')->change();
            $table->text('information_management_memo')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_constitution_plans', function (Blueprint $table) {
            $table->text('url')->change();
        });
    }
}
