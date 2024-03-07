<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBasicKnowledgeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_basic_knowledges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('lp_order_id')->comment('LP構成ID');
            $table->text('question')->comment('疑問点')->nullable();
            $table->text('others')->comment('他社で掲載している証明書など')->nullable();
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
        Schema::dropIfExists('t_basic_knowledges');
    }
}
