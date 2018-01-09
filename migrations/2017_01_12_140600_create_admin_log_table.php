<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAdminLogTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('admin_log'))
            Schema::create('admin_log', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('user_id');
                $table->string('action', 15);
                $table->integer('content_type_id');
                $table->dateTime('action_date');
            });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('admin_log');
    }

}
