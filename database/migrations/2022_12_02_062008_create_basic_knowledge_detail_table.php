<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBasicKnowledgeDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_basic_knowledge_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('basic_knowledge_id')->comment('基礎知識ID');
            $table->string('title')->comment('基礎知識項目のタイトル')->nullable();
            $table->text('detail')->comment('基礎知識項目の内容')->nullable();
            $table->integer('col')->comment('列番号');
            $table->integer('sort_order')->comment('並び順')->nullable();
            // システムカラム
            $table->string('created_pg')->comment('作成プログラム')->nullable();
            $table->dateTime('created_at')->comment('作成日時')->nullable();
            $table->string('updated_pg')->comment('更新プログラム')->nullable();
            $table->dateTime('updated_at')->comment('更新日時')->nullable();
            $table->string('deleted_pg')->comment('削除プログラム')->nullable();
            $table->dateTime('deleted_at')->comment('削除日時')->nullable();

            // 外部キー設定
            $table->foreign('basic_knowledge_id')->references('id')->on('t_basic_knowledges');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_basic_knowledge_details');
    }
}
