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

        \DB::table('users')->delete();

//        Schema::table('users', function (Blueprint $table) {
//            $table->foreign('role_id')->references('id')->on('roles');
//        });
        \DB::table('users')->insert(array(
            0 =>
                array(
                    'id' => '1',
                    'email' => 'admin@gmail.com',
                    'name' => 'Admin',
                    'password' => '$2y$10$AZWq6d4oLwmJiHQ82wzBjeKX0n8q7HlD7o92Fnw/PgNFZzob6hCae',
//                    'remember_token' => 'ofzVcCz9uxOsjfO4f314xhgfYzeFGgDKTjAtBa6pWICfXIdoUmgf4l5Vb1r1',
                    'role_id' => '1',
                    'updated_at' => '2016-12-22 13:23:42',
                    'deleted_at' => NULL,
                    'created_at' => '2016-11-30 10:12:09',
                ),
        ));
        \DB::table('users')->insert(array(
            0 =>
                array(
                    'id' => '2',
                    'email' => 'moderator@gmail.com',
                    'name' => 'Moderator',
                    'password' => '$2y$10$AZWq6d4oLwmJiHQ82wzBjeKX0n8q7HlD7o92Fnw/PgNFZzob6hCae',
//                    'remember_token' => 'ofzVcCz9uxOsjfO4f314xhgfYzeFGgDKTjAtBa6pWICfXIdoUmgf4l5Vb1r1',
                    'role_id' => '2',
                    'updated_at' => '2016-12-22 13:23:42',
                    'deleted_at' => NULL,
                    'created_at' => '2016-11-30 10:12:09',
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
        Schema::drop('users');
    }

}
