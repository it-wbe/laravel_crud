<?php
namespace Wbe\Crud\seeds;
use Illuminate\Database\Seeder;

class CreateRolesTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            0 => [
                'id' => '1',
                'name' => 'admin',
            ],
            1 => [
                'id' => '2',
                'name' => 'moderator',
            ],
        );

        $collection = collect($data);
        foreach ($collection as $row){
            if(is_null(\DB::table('roles')->where('name','=',$row['name'])->first())){
                \DB::table('roles')->insert($row->asArray());
            }
        }
    }
}
