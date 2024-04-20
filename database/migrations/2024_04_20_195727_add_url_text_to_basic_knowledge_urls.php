<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUrlTextToBasicKnowledgeUrls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_basic_knowledge_urls', function (Blueprint $table) {
            $table->string('url_text')->after('url')->comment('URLリンクのテキスト部分')->nullable();;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_basic_knowledge_urls', function (Blueprint $table) {
            $table->dropColumn('url_text');
        });
    }
}
