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


//        \DB::table('content_type')->delete();
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
