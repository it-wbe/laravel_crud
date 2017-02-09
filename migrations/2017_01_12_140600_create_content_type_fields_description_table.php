<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContentTypeFieldsDescriptionTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('content_type_fields_description'))
            Schema::create('content_type_fields_description', function (Blueprint $table) {
                $table->integer('content_id');
                $table->integer('lang_id');
                $table->string('title');
                $table->primary(['content_id', 'lang_id']);
            });

        \DB::table('content_type_fields_description')->delete();

        \DB::table('content_type_fields_description')->insert(array(
            0 =>
                array(
                    'content_id' => '1',
                    'lang_id' => '1',
                    'title' => 'Name',
                ),
            1 =>
                array(
                    'content_id' => '2',
                    'lang_id' => '1',
                    'title' => 'Email',
                ),
            2 =>
                array(
                    'content_id' => '3',
                    'lang_id' => '1',
                    'title' => 'Password',
                ),
            3 =>
                array(
                    'content_id' => '4',
                    'lang_id' => '1',
                    'title' => 'Confirm Password',
                ),
            4 =>
                array(
                    'content_id' => '1',
                    'lang_id' => '2',
                    'title' => 'Имя',
                ),
            5 =>
                array(
                    'content_id' => '2',
                    'lang_id' => '2',
                    'title' => 'Email',
                ),
            6 =>
                array(
                    'content_id' => '3',
                    'lang_id' => '2',
                    'title' => 'Пароль',
                ),
            7 =>
                array(
                    'content_id' => '4',
                    'lang_id' => '2',
                    'title' => 'Підтвердіть пароль',
                ),
            8 =>
                array(
                    'content_id' => '1',
                    'lang_id' => '3',
                    'title' => 'ESP name',
                ),
            9 =>
                array(
                    'content_id' => '3',
                    'lang_id' => '3',
                    'title' => 'ESP pass',
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
        Schema::drop('content_type_fields_description');
    }

}
