<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusDescriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasTable('menus_description'))
            Schema::create('menus_description', function (Blueprint $table) {
                $table->integer('content_id');
                $table->integer('lang_id');
                $table->string('title');
                $table->primary(['content_id', 'lang_id']);
            });

       // if(Schema::hasTable('menus')&&Schema::hasTable('menus_description')){
       //    $menu_tree = new Wbe\Crud\Controllers\MenuTreeController();
       //     $menu_tree->tree_generate(true);
       // }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menus_description');
    }
}
