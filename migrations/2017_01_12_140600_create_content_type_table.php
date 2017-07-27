<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Seeder;

class CreateContentTypeTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('content_type'))
            Schema::create('content_type', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('table');
                $table->string('model');
                $table->integer('sort');
                $table->integer('is_system');
            });


        \DB::table('content_type')->delete();

        \DB::table('content_type')->insert(array(
            0 =>
                array(
                    'id' => '1',
                    'table' => 'content_type',
                    'model' => 'Wbe\\Crud\\Models\\ContentTypes\\ContentType',
                    'sort' => '0',
                    'is_system' => '1',
                ),
            1 =>
                array(
                    'id' => '2',
                    'table' => 'content_type_fields',
                    'model' => 'Wbe\\Crud\\Models\\ContentTypes\\ContentTypeFields',
                    'sort' => '1',
                    'is_system' => '1',
                ),
            2 =>
                array(
                    'id' => '4',
                    'table' => 'languages',
                    'model' => 'Wbe\\Crud\\Models\\ContentTypes\\Languages',
                    'sort' => '3',
                    'is_system' => '1',
                ),
            3 =>
                array(
                    'id' => '3',
                    'table' => 'users',
                    'model' => 'Wbe\\Crud\\Models\\ContentTypes\\User',
                    'sort' => '2',
                    'is_system' => '1',
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
