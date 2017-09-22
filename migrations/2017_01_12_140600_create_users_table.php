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
        if (!Schema::hasTable('users'))
            Schema::create('users', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('email')->nullable();
                $table->string('name');
                $table->string('password');
                $table->string('remember_token')->nullable();
                $table->integer('role_id');
                $table->timestamps();
                $table->softDeletes();
            });

//        \DB::table('users')->delete();

//        Schema::table('users', function (Blueprint $table) {
//            $table->foreign('role_id')->references('id')->on('roles');
//        });
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
