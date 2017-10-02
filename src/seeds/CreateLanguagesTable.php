<?php
namespace Wbe\Crud\seeds;
use Illuminate\Database\Seeder;

class CreateLanguagesTable extends Seeder
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
                    'name' => 'English',
                    'code' => 'en',
                    'sort' => '1',
                ),
            1 =>
                array(
                    'id' => '2',
                    'name' => 'Русский',
                    'code' => 'ru',
                    'sort' => '2',
                ),
            2 =>
                array(
                    'id' => '3',
                    'name' => 'Українська',
                    'code' => 'ua',
                    'sort' => '3',
                ),
        );
        $collection = collect($data);
        foreach ($collection as $row){
            if(is_null(\DB::table('languages')->where('code','=',$row['code'])->first())){
                \DB::table('languages')->insert($row);
            }
        }
    }
}
