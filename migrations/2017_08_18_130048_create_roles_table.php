<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasTable('roles'))
            Schema::create('roles', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('name');
            });

        \DB::table('roles')->insert(array(
            0 =>
                array(
                    'id' => '1',
                    'name' => 'admin',
                    ),
            1=>[
                'id'=>'2',
                'name'=>'moderator',
            ],
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
