<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeKarteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_item_kartes', function (Blueprint $table) {
            $table->text('goods_name')->change();
            $table->text('goods_specifications')->change();
            $table->text('target_statue')->change();
            $table->text('BM_goods_name1')->change();
            $table->text('BM_url1')->change();
            $table->text('strong_point')->change();
            $table->text('memo')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_item_kartes', function (Blueprint $table) {
            $table->string('goods_name')->change();
            $table->string('goods_specifications')->change();
            $table->string('target_statue')->change();
            $table->string('BM_goods_name1')->change();
            $table->string('BM_url1')->change();
            $table->string('strong_point')->change();
            $table->string('memo')->change();
        });
    }
}
