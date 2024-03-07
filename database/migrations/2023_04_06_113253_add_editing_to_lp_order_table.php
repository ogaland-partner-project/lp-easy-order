<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEditingToLpOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_lp_orders', function (Blueprint $table) {
            $table->integer('editing')->after('description')->comment('0:未編集 1:編集中');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_lp_orders', function (Blueprint $table) {
            $table->dropColumn('editing');
        });
    }
}
