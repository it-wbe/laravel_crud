<?php
namespace Wbe\Crud\seeds;
use Illuminate\Database\Seeder;

class CreateUsersTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $data = array(
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
            1 =>
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
        );
        $collection = collect($data);
        foreach ($collection as $row){
            if(is_null(\DB::table('users')->where('name','=',$row['name'])->first())){
                \DB::table('users')->insert($row->asArray());
            }
        }
    }
}
