<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpgradeUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		if (Schema::hasColumn('users', 'settings'))
		{
			Schema::table('users', function($table) {
				$table->text('settings')->nullable();
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
        Schema::table('users', function($table) {
			$table->dropColumn('settings');
		});
    }
}
