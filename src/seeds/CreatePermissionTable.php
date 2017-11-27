<?php
namespace Wbe\Crud\seeds;
use Illuminate\Database\Seeder;

class CreatePermissionTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $temp = new \Wbe\Crud\Controllers\Rapyd\EditController();
//        echo'generate permission';
        $temp->regen_menu_permission();
    }
}
