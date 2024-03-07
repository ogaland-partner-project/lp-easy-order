<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemKartesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_item_kartes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('lp_order_id');
            $table->string('goods_name')->comment('正式な商品名')->nullable();
            $table->string('goods_specifications')->comment('商品仕様')->nullable();
            $table->integer('price_including_tax')->comment('税込販売価格')->nullable();
            $table->text('concept')->comment('コンセプト')->nullable();
            $table->string('target_jendar')->comment('ターゲット性別')->nullable();
            $table->integer('target_age')->comment('ターゲット年齢層')->nullable();
            $table->string('target_statue')->comment('ターゲット人間層')->nullable();
            $table->string('BM_goods_name1')->comment('BM商品名')->nullable();
            $table->string('BM_url1')->comment('BM URL')->nullable();
            $table->text('difference_point')->comment('差別化ポイント')->nullable();
            $table->string('strong_point')->comment('強み')->nullable();
            $table->string('memo')->comment('メモ')->nullable();
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
        Schema::dropIfExists('t_item_kartes');
    }
}
