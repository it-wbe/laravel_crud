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

        if (!Schema::hasTable('ct_to_relations'))
            Schema::create('ct_to_relations', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('relations_id');
                $table->integer('ct_to_relations_id');
                $table->string('ct_to_relations_type');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ct_to_relations');
    }
}
