<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLevelSelectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_level_selects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('lp_order_id')->comment('LP構成ID');
            $table->string('promoter')->comment('販促担当者')->nullable();
            $table->string('configurator')->comment('構成担当者')->nullable();
            $table->string('designer')->comment('デザイン担当者')->nullable();
            $table->integer('level')->comment('レベル 1~4')->nullable();
            $table->text('purpose')->comment('訴求内容、目的')->nullable();
            $table->string('point1')->comment('ポイント1')->nullable();
            $table->string('point2')->comment('ポイント2')->nullable();
            $table->string('point3')->comment('ポイント3')->nullable();
            $table->text('taste')->comment('デザイン・テイスト')->nullable();
            // システムカラム
            $table->string('created_pg')->comment('作成プログラム')->nullable();
            $table->dateTime('created_at')->comment('作成日時')->nullable();
            $table->string('updated_pg')->comment('更新プログラム')->nullable();
            $table->dateTime('updated_at')->comment('更新日時')->nullable();
            $table->string('deleted_pg')->comment('削除プログラム')->nullable();
            $table->dateTime('deleted_at')->comment('削除日時')->nullable();
            // 外部キー設定
            $table->foreign('lp_order_id')->references('id')->on('t_lp_orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_level_selects');
    }
}
