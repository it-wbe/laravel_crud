<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelationsDescriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasTable('relations_description'))
            Schema::create('relations_description', function (Blueprint $table) {
                $table->integer('content_id');
                $table->integer('lang_id');
                $table->string('name');
                $table->primary(['content_id', 'lang_id']);
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('relations_description');
    }
}
