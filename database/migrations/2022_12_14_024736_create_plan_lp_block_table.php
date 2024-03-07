<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanLpBlockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_plan_lp_blocks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('lp_order_id')->comment('LP構成ID');
            $table->string('block_detail')->comment('ブロック内容')->nullable();
            $table->text('requester_fix')->comment('依頼者修正メモ')->nullable();
            $table->text('pharmaceutical_affairs_fix')->comment('薬事チェック後修正')->nullable();
            $table->text('information_management_memo')->comment('情報管理メモ')->nullable();
            $table->integer('sort_order')->comment('並び順')->nullable();
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
        Schema::dropIfExists('t_plan_lp_blocks');
    }
}
