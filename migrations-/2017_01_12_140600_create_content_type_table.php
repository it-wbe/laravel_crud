<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Seeder;

class CreateContentTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('content_type'))
		Schema::create('content_type', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('table');
			$table->string('model');
			$table->integer('sort');
		});
		
		
		\DB::table('content_type')->delete();
        
        \DB::table('content_type')->insert(array (
            0 => 
            array (
                'id' => '5',
                'table' => 'customer',
                'model' => 'App\\Models\\ContentTypes\\Customer',
                'sort' => '4',
            ),
            1 => 
            array (
                'id' => '10',
                'table' => 'teams',
                'model' => 'App\\Models\\ContentTypes\\Teams',
                'sort' => '8',
            ),
            2 => 
            array (
                'id' => '1',
                'table' => 'content_type',
                'model' => 'Wbe\\Crud\\Models\\ContentTypes\\ContentType',
                'sort' => '0',
            ),
            3 => 
            array (
                'id' => '2',
                'table' => 'content_type_fields',
                'model' => 'Wbe\\Crud\\Models\\ContentTypes\\ContentTypeFields',
                'sort' => '1',
            ),
            4 => 
            array (
                'id' => '12',
                'table' => 'news',
                'model' => 'App\\Models\\ContentTypes\\News',
                'sort' => '10',
            ),
            5 => 
            array (
                'id' => '7',
                'table' => 'markets',
                'model' => 'App\\Models\\ContentTypes\\Markets',
                'sort' => '5',
            ),
            6 => 
            array (
                'id' => '8',
                'table' => 'outcomes',
                'model' => 'App\\Models\\ContentTypes\\Outcome',
                'sort' => '6',
            ),
            7 => 
            array (
                'id' => '9',
                'table' => 'packages',
                'model' => 'App\\Models\\ContentTypes\\Package',
                'sort' => '7',
            ),
            8 => 
            array (
                'id' => '4',
                'table' => 'languages',
                'model' => 'Wbe\\Crud\\Models\\ContentTypes\\Languages',
                'sort' => '3',
            ),
            9 => 
            array (
                'id' => '11',
                'table' => 'countries',
                'model' => 'App\\Models\\ContentTypes\\Country',
                'sort' => '9',
            ),
            10 => 
            array (
                'id' => '3',
                'table' => 'users',
                'model' => 'Wbe\\Crud\\Models\\ContentTypes\\User',
                'sort' => '2',
            ),
        ));
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('content_type');
	}

}
