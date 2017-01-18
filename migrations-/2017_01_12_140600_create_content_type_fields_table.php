<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContentTypeFieldsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('content_type_fields', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('content_type_id');
			$table->integer('sort');
			$table->string('name');
			$table->string('type');
			$table->string('display_column');
			$table->string('search_columns');
			$table->string('relation');
			$table->string('validators', 512);
			$table->boolean('grid_show');
			$table->boolean('grid_filter');
			$table->string('grid_custom_display', 512);
			$table->string('grid_attributes', 512);
			$table->boolean('form_show');
			$table->string('form_attributes', 512);
			$table->boolean('show');
			$table->unique(['content_type_id','name'], 'crud_content_type_id,name');
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
