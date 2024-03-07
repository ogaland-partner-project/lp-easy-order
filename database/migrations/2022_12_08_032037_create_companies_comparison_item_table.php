<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesComparisonItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_companies_comparison_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('companies_header_id')->comment('比較構成ヘッダーID');
            $table->text('text')->comment('比較構成リストの内容')->nullable();
            $table->text('color')->comment('セルの色')->nullable();
            $table->integer('col')->comment('比較構成列番号')->nullable();
            // システムカラム
            $table->string('created_pg')->comment('作成プログラム')->nullable();
            $table->dateTime('created_at')->comment('作成日時')->nullable();
            $table->string('updated_pg')->comment('更新プログラム')->nullable();
            $table->dateTime('updated_at')->comment('更新日時')->nullable();
            $table->string('deleted_pg')->comment('削除プログラム')->nullable();
            $table->dateTime('deleted_at')->comment('削除日時')->nullable();
            // 外部キー設定
            $table->foreign('companies_header_id')->references('id')->on('t_companies_comparison_headers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_companies_comparison_items');
    }
}
