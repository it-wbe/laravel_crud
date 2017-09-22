<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContentTypeFieldsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('content_type_fields'))
            Schema::create('content_type_fields', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('content_type_id');
                $table->integer('sort')->nullable();
                $table->string('name', 128);
                $table->string('type')->nullable();
                $table->string('display_column')->nullable();
                $table->string('search_columns')->nullable();
                $table->string('relation')->nullable();
                $table->string('validators', 512)->nullable();
                $table->boolean('grid_show')->nullable();
                $table->boolean('grid_filter')->nullable();
                $table->string('grid_custom_display', 512)->nullable();
                $table->string('grid_attributes', 512)->nullable();
                $table->boolean('form_show')->nullable();
                $table->string('form_attributes', 512)->nullable();
                $table->boolean('show')->nullable();
                $table->unique(['content_type_id', 'name'], 'crud_content_type_id,name')->nullable();
            });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('content_type_fields');
    }

}
