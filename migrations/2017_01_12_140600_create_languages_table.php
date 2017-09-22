<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLanguagesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('languages'))
            Schema::create('languages', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('name');
                $table->string('code', 2);
                $table->integer('sort');
            });


//        \DB::table('languages')->delete();

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('languages');
    }

}
