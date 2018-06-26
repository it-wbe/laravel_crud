<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CrudCreateUsersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users')){
            Schema::create('users', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('email')->nullable();
                $table->string('name');
                $table->string('password');
                $table->string('remember_token')->nullable();
                $table->integer('role_id');
                $table->text('settings')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
		}else{
			Schema::table('users',function($table){
                $table->timestamp('deleted_at')->nullable();
				$table->text('settings')->nullable();
                $table->integer('role_id');
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
