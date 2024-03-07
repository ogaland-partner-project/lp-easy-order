<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeProcessCatchphrasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_constitution_catchphrases', function (Blueprint $table) {
            $table->text('catchphrase')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_constitution_catchphrases', function (Blueprint $table) {
            $table->string('catchphrase')->change();
        });
    }
}
