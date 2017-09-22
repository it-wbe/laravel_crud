<?php
namespace Wbe\Crud\seeds;
use Illuminate\Database\Seeder;

class CreateContentTypeFieldsDescriptionTable extends Seeder
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
                    'content_id' => '1',
                    'lang_id' => '2',
                    'name' => 'Типы Контента',
                ),
            1 =>
                array(
                    'content_id' => '1',
                    'lang_id' => '1',
                    'name' => 'Content Types',
                ),
            2 =>
                array(
                    'content_id' => '1',
                    'lang_id' => '3',
                    'name' => 'Типи Контенту',
                ),
            3 =>
                array(
                    'content_id' => '2',
                    'lang_id' => '1',
                    'name' => 'Content Type Fields',
                ),
            4 =>
                array(
                    'content_id' => '2',
                    'lang_id' => '2',
                    'name' => 'Поля Типов Контента',
                ),
            5 =>
                array(
                    'content_id' => '2',
                    'lang_id' => '3',
                    'name' => 'Поля Типів Контенту',
                ),

            6 =>
                array(
                    'content_id' => '3',
                    'lang_id' => '1',
                    'name' => 'Administrators',
                ),
            7 =>
                array(
                    'content_id' => '3',
                    'lang_id' => '2',
                    'name' => 'Администраторы',
                ),
            8 =>
                array(
                    'content_id' => '3',
                    'lang_id' => '3',
                    'name' => 'Адміністратори',
                ),
            9 =>
                array(
                    'content_id' => '4',
                    'lang_id' => '1',
                    'name' => 'Languages',
                ),
            10 =>
                array(
                    'content_id' => '4',
                    'lang_id' => '2',
                    'name' => 'Мови',
                ),
            11 =>
                array(
                    'content_id' => '4',
                    'lang_id' => '3',
                    'name' => 'Мови',
                ),
            12 =>
                array(
                    'content_id' => '5',
                    'lang_id' => '1',
                    'name' => 'Roles',
                ),
            13 =>
                array(
                    'content_id' => '5',
                    'lang_id' => '2',
                    'name' => 'Роли',
                ),
            14 =>
                array(
                    'content_id' => '5',
                    'lang_id' => '3',
                    'name' => 'Роли',
                ),
            15 =>
                array(
                    'content_id' => '6',
                    'lang_id' => '1',
                    'name' => 'Related Content Type',
                ),
            16 =>
                array(
                    'content_id' => '6',
                    'lang_id' => '2',
                    'name' => 'Связанный контент',
                ),
            17 =>
                array(
                    'content_id' => '6',
                    'lang_id' => '3',
                    'name' => 'Звязанний контент',
                ),

        );
        /// $max_cont_id получаем максимальный контент на данный момент
        /// $cont_type_id  получаем id которые больше $max_cont_id  - получем количество и id контента которые добавили
        ///
        $max_cont_id  = \DB::table('content_type_description')->max('content_id');
        $cont_type_id = \DB::table('content_type')->where('id','>',$max_cont_id)->get(['id']);
        if(empty($cont_type_id)){
            \DB::table('content_type_description')->insert($data);
        }
        else{ // меняем значения id
            $collect = collect($data)->groupBy('content_id');
            $data = $collect->slice($collect->count() - count($cont_type_id));
            $data->all();
            $i = 0;
            foreach ($data as $data_temp_key=> $data_temp_value){
                foreach ($data_temp_value as $row_value){
                    $row_value['content_id'] =$cont_type_id[$i]->id;
                    \DB::table('content_type_description')->insert($row_value);
                }
                $i++;
            }
        }
    }
}
