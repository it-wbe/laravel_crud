<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Wbe\Crud\Controllers\Rapyd;

class CreatePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasTable('permissions')){
            Schema::create('permissions', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('name',255);
				$table->text('href');
				$table->tinyInteger('del');
            });
			Schema::create('permissions_role', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('role_id');
				$table->integer('permissions_id');
				$table->tinyInteger('r');
				$table->tinyInteger('w');
				$table->tinyInteger('d');
            });
			$temp = new EditController();
			$temp->regen_menu_permission();
		}
		
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('permissions_role');
    }
}
