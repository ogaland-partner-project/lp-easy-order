<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTItemKartesTableColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_item_kartes', function (Blueprint $table) {
            // 型をint→stringに変更
            $table->string('target_age')->default(NULL)->comment('ターゲット年齢層')->nullable()->change();
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
            // 元に戻し用
            $table->integer('target_age')->comment('ターゲット年齢層')->nullable()->change();
        });
    }
}
