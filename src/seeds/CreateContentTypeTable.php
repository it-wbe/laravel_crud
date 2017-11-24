<?php
namespace Wbe\Crud\seeds;
use Illuminate\Database\Seeder;

class CreateContentTypeTable extends Seeder
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
                    'table' => 'content_type',
                    'model' => 'Wbe\\Crud\\Models\\ContentTypes\\ContentType',
                    'sort' => '0',
                    'is_system' => '1',
                ),
            1 =>
                array(
                    'id' => '2',
                    'table' => 'content_type_fields',
                    'model' => 'Wbe\\Crud\\Models\\ContentTypes\\ContentTypeFields',
                    'sort' => '1',
                    'is_system' => '1',
                ),

            2 =>
                array(
                    'id' => '3',
                    'table' => 'users',
                    'model' => 'Wbe\\Crud\\Models\\ContentTypes\\User',
                    'sort' => '2',
                    'is_system' => '1',
                ),
            3 =>
                array(
                    'id' => '4',
                    'table' => 'languages',
                    'model' => 'Wbe\\Crud\\Models\\ContentTypes\\Languages',
                    'sort' => '3',
                    'is_system' => '1',
                ),
//            4 =>
//                array(
//                    'id' => '5',
//                    'table' => 'roles',
//                    'model' => 'Wbe\\Crud\\Models\\ContentTypes\\Role',
//                    'sort' => '4',
//                    'is_system' => '1',
//                ),
            5 =>
                array(
                    'id' => '6',
                    'table' => 'relations',
                    'model' => 'Wbe\\Crud\\Models\\ContentTypes\\Relations',
                    'sort' => '5',
                    'is_system' => '1',
                ),
        );
            foreach ($data as $key=> $value){
              $exsist =   \DB::table('content_type')->where([['id','=',$value['id']],['table','=',$value['table']]])->first();
//              dd($exsist);
              if(is_null($exsist)){
                  unset($value['id']);
                  \DB::table('content_type')->insert($value);
              }
        }
    }
}
