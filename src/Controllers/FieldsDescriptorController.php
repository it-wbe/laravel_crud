<?php

namespace Wbe\Crud\Controllers;

use Wbe\Crud\Models\ModelGenerator;
use Wbe\Crud\Models\Globals;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Wbe\Crud\Models\ContentTypes\Languages;
use Wbe\Crud\Models\ContentTypes\ContentType;
use Wbe\Crud\Models\ContentTypes\ContentTypeFields;
use Zofe\Rapyd\DataForm\Field\Ckeditor;


class FieldsDescriptorController extends Controller
{
    /** @var string Строка для визначення, чи перегенеровувати всю модель */
    //const regenerate_entire_model_ident = '[leave this text to regenerate entire model]';
    /** @var array Масив відношень */
    const relations = ['hasOne', 'hasMany', 'belongsTo', 'belongsToMany','morphToMany','morphedByMany'];
    /** @var array Масив описів відношень */
    const relations_descriptions = ['hasOne', 'hasMany', 'belongsTo (інверсія hasMany)', 'belongsToMany'];
    /** @var array Масив виключених з генерації колонок */
    const excluded_fields = ['lang_id', 'content_id'];

    /**
     * Поля контенту (admin/fields_descriptor/content/*)
     * @param Request $r запит із роутів
     * @param int $content_type ID типу контенту
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function content_types(Request $r, $content_type)
    {
        $unknown_methods = [];

        $content = ContentType::find($content_type);

        $generate_fields = \Request::exists('btn-generate-fields');

        //print_r($_POST);


        ModelGenerator::generateModelByTable($content->table, $content->table . '_description', $content->model);


        if (\Request::isMethod('post')) {
            if (\Request::input('active_tab') == 'fields') {
                if (\Request::exists('btn-save-fields')) {

                    //!!!
                    \DB::table('content_type_fields')->where(['content_type_id' => $content_type])->delete();

                    if (\Request::input('name')) {

                        if(!is_array(\Request::input('name')))
                            $names = [\Request::input('name')];
                        else
                            $names = \Request::input('name');

                        //print_r(\Request::all());

                        foreach ($names as $k => $f) {
                            $field = [
                                'name' => $names[$k],
                                'content_type_id' => $content_type,
                                'type' => \Request::input('type')[$k],
                                'display_column' => \Request::input('display_column')[$k],
                                'search_columns' => \Request::input('search_columns')[$k],
                                'relation' => \Request::input('relation')[$k],
                                'validators' => \Request::input('validators')[$k],
                                'grid_show' => (int)(\Request::input('grid_show')[$k] == 'on'),
                                'grid_filter' => (int)(\Request::input('grid_filter')[$k] == 'on'),
                                'grid_custom_display' => \Request::input('grid_custom_display')[$k],
                                'grid_attributes' => \Request::input('grid_attributes')[$k],
                                'form_show' => (int)(\Request::input('form_show')[$k] == 'on'),
                                'form_attributes' => \Request::input('form_attributes')[$k],
                                'show' => (int)(\Request::input('show')[$k] == 'on'),
                                'sort' => (int)\Request::input('sort')[$k],
                            ];

                            if (\Request::input('id')[$k]&& \Request::input('id')[$k]>0) {
                                $field['id'] = \Request::input('id')[$k];
//                                if($names[$k]=='_default'){
//                                    $field['id'] = null;
//                                }
//                                dump('updateOrInsert',$field);
                                ContentTypeFields::updateOrInsert(['id' => $field['id']], $field);
                            } else{
//                                dump('insert');
                                ContentTypeFields::insert($field);
                            }


                            //ContentTypeFields::updateOrInsert(['id' => $field['id']], $field);
                        }
//                        dd();
                        unset($f);

                        Globals::$messages[1][] = 'Описи полів збережено';
                    } //else $this->messages[0][] = 'Нічого зберігати';
                }/* elseif (\Request::exists('btn-clear-fields')) {
                    $this->messages[1][] = 'Поля очищено (no function)';
                }*/
            } elseif (\Request::input('active_tab') == 'relations') {

                if (\Request::input('existing_relations')) {
                    $req_existing_relations = explode(',', \Request::input('existing_relations'));
                }
                else $req_existing_relations = false;

                if (\Request::input('rel_method_name'))
                    $rel_method_names = \Request::input('rel_method_name');
                else $rel_method_names = [];

                // Видалення існуючих зв'язків
                if ($req_existing_relations)
                {
                    $left_relations = [];
                    foreach ($req_existing_relations as $ex_rel) {
                        if (!in_array($ex_rel, $rel_method_names)) {
                            $left_relations[trim($ex_rel)] = '';
                            Globals::$messages[2][] = 'Зв\'язок ' . $ex_rel . ' видалено';
                        }
                    }
                    if ($left_relations)
                        ModelGenerator::write_content_model($content, $left_relations);
                }

                $left_relations = [];
                //XEDIT
                if ($rel_method_names)
                    foreach ($rel_method_names as $k => $rel_method_name) {

                        if (!isset(\Request::input('rel_type')[$k])) {
                            echo 'cannot generate relation';
                            continue;
                        }

                        $rp = [
                            'rel_type' => \Request::input('rel_type')[$k],
                            'right_name' => trim(\Request::input('rel_method_name')[$k]),
                            'right_content_id' => \Request::input('rel_right_content_type')[$k],
                            'left_column' => \Request::input('rel_left_column')[$k],
                            'right_column' => \Request::input('rel_right_column')[$k],
                            'rel_table_to' => trim(\Request::input('rel_table_to')[$k]),
                        ];

                        if ($rp['rel_type'] == 'belongsTo') {
                            $tmp = $rp['left_column'];
                            $rp['left_column'] = $rp['right_column'];
                            $rp['right_column'] = $tmp;
                        }

                            $right_content = ContentType::find($rp['right_content_id']);

                        ModelGenerator::get_content_model_relation($content, $right_content, $rp, $left_relation);

                        if (!$left_relation) continue;

                        $left_relations[$rel_method_name] = $left_relation;

                        /*echo $left_relation;
                        echo '--------------';
                        echo $right_relation;*/

                        //$this->write_content_model($right_content, [$content->table => $right_relation]);

                        if ($rp['rel_type'] == 'belongsToMany') {

                            $to_left_field_name = $content->table;
                            $to_right_field_name = $right_content->table;

                            if ($rp['rel_table_to']) {
                                $to_table_name = $rp['rel_table_to'];
                            } else {
                                $to_table_name = $to_left_field_name .
                                    '_to_' .
                                    $to_right_field_name;
                            }
                            /*\Schema::create($table_name, function (\Illuminate\Database\Schema\Blueprint $table) {
                                $table->increments('id');
                            });*/
                            if (\Schema::hasTable($to_table_name)) {
                                $to_cols = \Schema::getColumnListing($to_table_name);
                                if (in_array($to_left_field_name, $to_cols) && in_array($to_right_field_name, $to_cols))
                                    Globals::$messages[1][] = 'Таблиця "' . $to_table_name . '" вже існує, і схожа на коректну (відношення belongsToMany)';
                                else
                                    Globals::$messages[3][] = 'Таблиця "' . $to_table_name . '" вже існує, і не має потрібних колонок (відношення belongsToMany)';
                            } else {
                                //$left_field_name = trim(after('.', \Request::input('rel_left_column')[$k]));
                                //$right_field_name = trim(after('.', \Request::input('rel_right_column')[$k]));
                                $left_field_name = trim(after('.', \Request::input('rel_left_column')[$k]));
                                $right_field_name = trim(after('.', \Request::input('rel_right_column')[$k]));

                                $left_field = \DB::select('SHOW COLUMNS FROM `' . $content->table . '` WHERE Field = "' . $left_field_name . '"');
                                $right_field = \DB::select('SHOW COLUMNS FROM `' . $right_content->table . '` WHERE Field = "' . $right_field_name . '"');
                                if (!$left_field) {
                                    Globals::$messages[3][] = 'Поле "' . $left_field_name . '" не знайдено в таблиці "' . $content->table . '"';
                                    continue;
                                }
                                if (!$right_field) {
                                    Globals::$messages[3][] = 'Поле "' . $right_field_name . '" не знайдено в таблиці "' . $right_content->table . '"';
                                    continue;
                                }
                                $left_field = $left_field[0];
                                $right_field = $right_field[0];

                                $sql = 'CREATE TABLE `' . $to_table_name . '` (
                                      `' . $to_left_field_name . '` ' . $left_field->Type . ' NOT NULL,
                                      `' . $to_right_field_name . '` ' . $right_field->Type . ' NOT NULL,
                                      PRIMARY KEY (`' . $to_left_field_name . '`,`' . $to_right_field_name . '`)
                                    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
                                ';
                                \DB::statement($sql);

                                Globals::$messages[1][] = 'Таблицю "' . $to_table_name . '" відношення belongsToMany створено (SQL: "' . $sql . '")';

                            }
                        }
                    }

                if ($left_relations)
                    ModelGenerator::write_content_model($content, $left_relations);

                unset($f);
            }
        }


        $classname = $content::getCTModel($content->model);
        //if (!$classname) echo '!$classname';
        $model_filename = $content->getClassFilename($classname);
        if (!$model_filename)
            die('model not found: ' . $content->model);
        //base_path() . '\app\Models\\' . ltrim($content->model, '\\/') . '.php';
        $relation_methods = ModelGenerator::getModelRelationsMethods(file_get_contents($model_filename));
        //print_r($relation_methods); die();


        $existing_relations = [];
        $relations = [];

        foreach ($relation_methods as $rel_m_k => $rel_m) {
            //print_r($rel_m[1]);
            ///$right_content = ContentType::where('table', $rel_m[0])->first()

            if (isset($rel_m[2]) && isset($rel_m[2][0]) && (in_array($rel_m[1], self::relations))) {
                //print_r($rel_m);
                //$relation_methods[$rel_m_k]

                $rel_type = $rel_m[1];
                //print_r($rel_m);


                //$right_model = str_replace('App\Models\ContentTypes\\', '', $rel_m[2][0]);
                $right_model = $rel_m[2][0];
                $right_content = ModelGenerator::getContentTypeByModel($right_model);

                if (!$right_content) {
                    //die('cannot find content type with model = ' . $right_model);
                    Globals::$messages[2][] = 'public function ' . $rel_m_k . '(): cannot find content type with model "' . $right_model . '" - ignored';
                    continue;
                }


                $rel_key = $rel_m_k;

                if($rel_type == 'morphedByMany'|| $rel_type =='morphToMany'){
                    $relations[$rel_key] = [
                        'rel_right_content_type' => $right_content->id,
                        'rel_method_name' => $rel_key,
                        'rel_type' => $rel_type,
//                      'rel_left_column' => $rel_m[2][2],
                        'rel_right_column' => $rel_m[2][1],
                    ];
                }else{
                    $relations[$rel_key] = [
                        'rel_right_content_type' => $right_content->id,
                        'rel_method_name' => $rel_key,
                        'rel_type' => $rel_type,
                        'rel_left_column' => $rel_m[2][2],
                        'rel_right_column' => $rel_m[2][1],
                    ];
                }
                if ($rel_type == 'belongsToMany') {
                    if (isset($rel_m[2][1]))
                        $relations[$rel_key]['rel_table_to'] = $rel_m[2][1];
                    else
                        $relations[$rel_key]['rel_table_to'] = $content->table . '_to_' . $right_content->table;
                    $relations[$rel_key]['rel_left_column'] = isset($rel_m[2][2]) ? $rel_m[2][2] : 'id';
                    $relations[$rel_key]['rel_right_column'] = isset($rel_m[2][3]) ? $rel_m[2][3] : 'id';
                    //print_r($rel_m[2][2]);

                    //$relations[$rel_key]['rel_table_to'] = $rel_m[2][3];
                } elseif ($rel_type == 'belongsTo') {
                    $relations[$rel_key]['rel_table_to'] = $content->table . '_to_' . $right_content->table;
                    $relations[$rel_key]['rel_left_column'] = isset($rel_m[2][1]) ? $rel_m[2][1] : 'id';
                    $relations[$rel_key]['rel_right_column'] = isset($rel_m[2][2]) ? $rel_m[2][2] : 'id';
                } else {
                    $relations[$rel_key]['rel_table_to'] = $content->table . '_to_' . $right_content->table;
                    $relations[$rel_key]['rel_left_column'] = isset($rel_m[2][2]) ? $rel_m[2][2] : 'id';
                    $relations[$rel_key]['rel_right_column'] = isset($rel_m[2][1]) ? $rel_m[2][1] : 'id';
                }

                $relations[$rel_key]['rel_table_to_exists'] = \Schema::hasTable($relations[$rel_key]['rel_table_to']);

                //($right_name, \Request::input('rel_type')[$k], $right_model, [$l_to_r_model_table, $left_column, $right_column]);

                $existing_relations[] = $rel_key;
            } else {
                $unknown_methods[] = $rel_m;
            }
        }

        //print_r($relations);


        $table = $content->table;
        $table_exists = \Schema::hasTable($table);

        $desc_table = $content->table . '_description';
        $desc_table_exists = \Schema::hasTable($desc_table);

        //$default_field = ContentTypeFields::find(0);
        $default_field = ContentTypeFields::where(['content_type_id' => -2])->first();
        if (!$default_field || !isset($default_field->name))
            die('Cannot find default field! Check field "_default" into "content_type_fields".');

        $fields = ContentTypeFields
            ::where('content_type_id', $content_type)
            ->orderBy('sort')
            ->get()
            ->keyBy('name');

        if (\Schema::hasTable($table)) {
            $table_fields = \DB::select('SHOW COLUMNS FROM ' . $table);
        } else $table_fields = [];
        if (\Schema::hasTable($desc_table)) {
            $desc_table_fields = \DB::select('SHOW COLUMNS FROM ' . $desc_table);
        } else $desc_table_fields = [];

        $table_all_fields = array_merge($table_fields, $desc_table_fields);
        foreach ($table_all_fields as $f) {
            if (isset($fields[$f->Field]))
                $fields[$f->Field]->exists_in_table = true;
        }
        if ($generate_fields)
            foreach ($table_all_fields as $f) {
                if (!isset($fields[$f->Field]) && (!in_array($f->Field, self::excluded_fields)))
                    $fields[$f->Field] = ModelGenerator::autofield($f, $content_type, $default_field);
            }

        /*foreach ($fields as $k => $f) {
            if (isset($fields[$f->Field]))
                $fields[$f->Field]->exists_in_table = true;
        }*/

        /*$field_types = [
            'text' => 'Text',
            'textarea' => 'Textarea',
            'hidden' => 'Hidden',
            'password' => 'Password',
            'file' => 'File',
            'image' => 'Image',
            'select' => 'Select',
            'radiogroup' => 'Radiogroup',
            'Wbe\Crud\Models\Rapyd\Fields\Ckeditor' => 'Ckeditor',
            'autocomplete' => 'Autocomplete',
            'tags' => 'Tags',
            'multiselect' => 'Multiselect',
            'number' => 'Number',
            'numberrange' => 'Numberrange',
            'colorpicker' => 'Colorpicker',
            'date' => 'Date',
            'daterange' => 'Daterange',
            'datetime' => 'Datetime',
            'iframe' => 'Iframe',
            'map' => 'Map',
            'checkbox' => 'Checkbox',
            //'Checkboxgroup' => 'Checkboxgroup',
            'container' => 'Container',
            'auto' => 'Auto',
            'redactor' => 'Redactor',
            //'rel:select' => 'Rel:Select',
            //'rel:autocomplete' => 'Rel:Autocomplete',
            //'rel:tags' => 'Rel:Tags',
            //'rel:multiselect' => 'Rel:Multiselect',
        ];*/

        //print_r($fields);

        $content_types = ContentType::get()->keyBy('id');
        //unset($content_types[$content_type]);

        $existing_relations = implode(',', $existing_relations);

        return view('crud::crud.fieldsdescriptor', [
            'default_field' => $default_field,
            'fields' => $fields,
            'field_types' => config('crud.field_types'),
            'content' => $content,
            'content_types' => $content_types,
            'relations' => $relations,
            'existing_relations' => $existing_relations,
            'messages' => Globals::$messages,
            'message_class' => Globals::$message_class,
            'unknown_methods' => $unknown_methods,
            'left_columns' => \Schema::getColumnListing($content->table),
        ]);
    }
}
