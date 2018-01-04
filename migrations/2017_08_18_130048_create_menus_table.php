<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasTable('menus'))
            Schema::create('menus', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('parent_id')->nullable();
                $table->integer('lft')->nullable();
                $table->integer('rgt')->nullable();
                $table->integer('depth')->nullable();
                $table->text('href')->nullable();
                $table->integer('item_type');
                $table->string('icon',70)->default("");
            });

        //if(Schema::hasTable('menus')&&Schema::hasTable('menus_description')){
        //    $menu_tree = new Wbe\Crud\Controllers\MenuTreeController();
        //    $menu_tree->tree_generate(true);
        //}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menus');
    }
}
