<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContentTypeFieldsDescriptionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('content_type_fields_description', function(Blueprint $table)
		{
			$table->integer('content_id');
			$table->integer('lang_id');
			$table->string('title');
			$table->primary(['content_id','lang_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('content_type_fields_description');
	}

}
