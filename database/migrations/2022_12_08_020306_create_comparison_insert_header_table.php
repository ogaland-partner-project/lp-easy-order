<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComparisonInsertHeaderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_comparison_insert_headers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('lp_order_id')->comment('LP構成ID');
            $table->string('header_name')->comment('ヘッダー名')->nullable();
            $table->string('header_type')->comment('ヘッダータイプ(文字列、画像、URL、計算)')->nullable();
            $table->string('calculation_type')->comment('計算タイプ')->nullable();
            $table->string('calculation_row')->comment('計算対象列')->nullable();
            $table->integer('comparison_insert_flag')->comment('1:構成比較入力で利用')->nullable();
            $table->integer('companies_comparison_flag')->comment('1:他社構成比較で利用')->nullable();
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
        Schema::dropIfExists('t_comparison_insert_headers');
    }
}
