<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConstitutionCatchphraseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_constitution_catchphrases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('constitution_process_id')->comment('構成の手順ID');
            $table->string('catchphrase')->comment('キャッチ内容')->nullable();
            $table->integer('sort_order')->comment('並び順')->nullable();
            // システムカラム
            $table->string('created_pg')->comment('作成プログラム')->nullable();
            $table->dateTime('created_at')->comment('作成日時')->nullable();
            $table->string('updated_pg')->comment('更新プログラム')->nullable();
            $table->dateTime('updated_at')->comment('更新日時')->nullable();
            $table->string('deleted_pg')->comment('削除プログラム')->nullable();
            $table->dateTime('deleted_at')->comment('削除日時')->nullable();
            // 外部キー設定
            $table->foreign('constitution_process_id')->references('id')->on('t_constitution_processes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_constitution_catchphrases');
    }
}
