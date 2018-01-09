<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users')){
            Schema::table('users', function ($table) {
                //$table->integer('id', true);
                $table->string('email')->nullable()->change();
                //$table->string('name');
                //$table->string('password');
                $table->string('remember_token')->nullable()->change();
                $table->integer('role_id');
                $table->text('settings')->nullable();
                //$table->timestamps();
                $table->softDeletes();
            });
		}
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }

}
