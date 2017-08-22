<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContentTypeDescriptionTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('content_type_description'))
            Schema::create('content_type_description', function (Blueprint $table) {
                $table->integer('content_id');
                $table->integer('lang_id');
                $table->string('name');
                $table->primary(['content_id', 'lang_id']);
                $table->index(['lang_id', 'name'], 'lang_id2name');
            });


        \DB::table('content_type_description')->delete();

        \DB::table('content_type_description')->insert(array(
            0 =>
                array(
                    'content_id' => '1',
                    'lang_id' => '2',
                    'name' => 'Типы Контента',
                ),
            1 =>
                array(
                    'content_id' => '1',
                    'lang_id' => '1',
                    'name' => 'Content Types',
                ),
            2 =>
                array(
                    'content_id' => '1',
                    'lang_id' => '3',
                    'name' => 'Типи Контенту',
                ),
            3 =>
                array(
                    'content_id' => '2',
                    'lang_id' => '1',
                    'name' => 'Content Type Fields',
                ),
            4 =>
                array(
                    'content_id' => '2',
                    'lang_id' => '2',
                    'name' => 'Поля Типов Контента',
                ),
            5 =>
                array(
                    'content_id' => '2',
                    'lang_id' => '3',
                    'name' => 'Поля Типів Контенту',
                ),
            6 =>
                array(
                    'content_id' => '4',
                    'lang_id' => '1',
                    'name' => 'Languages',
                ),
            7 =>
                array(
                    'content_id' => '4',
                    'lang_id' => '2',
                    'name' => 'Мови',
                ),
            8 =>
                array(
                    'content_id' => '4',
                    'lang_id' => '3',
                    'name' => 'Мови',
                ),
            9 =>
                array(
                    'content_id' => '3',
                    'lang_id' => '1',
                    'name' => 'Administrators',
                ),
            10 =>
                array(
                    'content_id' => '3',
                    'lang_id' => '2',
                    'name' => 'Администраторы',
                ),
            11 =>
                array(
                    'content_id' => '3',
                    'lang_id' => '3',
                    'name' => 'Адміністратори',
                ),
            12 =>
                array(
                    'content_id' => '5',
                    'lang_id' => '1',
                    'name' => 'Roles',
                ),
            13 =>
                array(
                    'content_id' => '5',
                    'lang_id' => '2',
                    'name' => 'Роли',
                ),
            14 =>
                array(
                    'content_id' => '5',
                    'lang_id' => '3',
                    'name' => 'Роли',
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
        Schema::drop('content_type_description');
    }

}
