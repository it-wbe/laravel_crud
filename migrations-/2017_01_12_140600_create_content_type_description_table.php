<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContentTypeDescriptionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('content_type_description', function(Blueprint $table)
		{
			$table->integer('content_id');
			$table->integer('lang_id');
			$table->string('name');
			$table->primary(['content_id','lang_id']);
			$table->index(['lang_id','name'], 'lang_id2name');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('content_type_description');
	}

}
