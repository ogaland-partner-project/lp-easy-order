<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLpOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_lp_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('product_name')->comment('LP構成名')->nullable();
            $table->string('product_code')->comment('品目コード')->nullable();
            $table->text('description')->comment('簡易説明')->nullable();
            $table->integer('status')->comment('0:着手中 1:薬事確認中 2:薬機確認中 3:完了')->nullable();
            $table->integer('requirement_flag')->comment('0:2年後自動的に削除される 1:削除されない')->nullable();
            // システムカラム
            $table->string('created_pg')->comment('作成プログラム')->nullable();
            $table->dateTime('created_at')->comment('作成日時')->nullable();
            $table->string('updated_pg')->comment('更新プログラム')->nullable();
            $table->dateTime('updated_at')->comment('更新日時')->nullable();
            $table->string('deleted_pg')->comment('削除プログラム')->nullable();
            $table->dateTime('deleted_at')->comment('削除日時')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_lp_orders');
    }
}
