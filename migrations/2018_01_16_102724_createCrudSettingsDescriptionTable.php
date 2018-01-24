<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrudSettingsDescriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('crud_settings_description')) {
            Schema::create('crud_settings_description', function (Blueprint $table) {
                $table->integer('content_id');
                $table->integer('lang_id');
                $table->string('meta_title', 255);
                $table->string('meta_description', 255);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crud_settings_description');
    }
}
