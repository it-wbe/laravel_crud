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


        \DB::table('languages')->delete();

        \DB::table('languages')->insert(array(
            0 =>
                array(
                    'id' => '1',
                    'name' => 'English',
                    'code' => 'en',
                    'sort' => '1',
                ),
            1 =>
                array(
                    'id' => '2',
                    'name' => '�������',
                    'code' => 'ru',
                    'sort' => '2',
                ),
            2 =>
                array(
                    'id' => '3',
                    'name' => '���������',
                    'code' => 'uk',
                    'sort' => '3',
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
        Schema::drop('languages');
    }

}
