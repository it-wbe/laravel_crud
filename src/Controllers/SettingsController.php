<?php

namespace Wbe\Crud\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Wbe\Crud\Models\ContentTypes\ContentTypeFields;
use Wbe\Crud\Models\ContentTypeFieldsDescription;
use Wbe\Crud\Models\ContentTypes\ContentType;


class SettingsController extends Controller
{
    static public function index()
    {

        return view('crud::crud.settings');
    }

    static public function getClassFilename($classname)
    {
        $reflector = new \ReflectionClass($classname);
        return $reflector->getFileName();
    }

    static public function generate()
    {
        return false;
        $default_field = ContentTypeFields::find(0);

        $content_types = ContentType::all()->keyBy('id');


        foreach ($content_types as $content_type_k => $content_type) {
            $table = $content_type->table;
            $desc_table = $table . '_description';

            if (\Schema::hasTable($table)) {

                // $content_description_model_template
                $cdm_template = \File::get(storage_path('generator/ContentModel.txt'));

                $classname = $content_type->model;

                $filename = '../app/Models/' . ltrim($classname, '\\/') . '.php';

                //(!class_exists('App\Models\\' . $classname)) ||
                if ((!file_exists($filename)) ||
                    (strpos(file_get_contents($filename), '[leave this text to regenerate]') !== false)
                ) {

                    $fields = \Schema::getColumnListing($table);
                    $no_timestamps = !(in_array('created_at', $fields) && in_array('updated_at', $fields) && in_array('deleted_at', $fields));
                    $translate = (\Schema::hasTable($desc_table));

                    $content = '';

                    $classname_namespace = before_last('\\', $classname, 1);
                    $cdm_template = bind_string($cdm_template, [
                        'namespace' => $classname_namespace ? /*'App\Models\\' .*/ $classname_namespace : 'App\Models',
                        'classname' => after_last('\\', $classname),
                        'table' => $table,
                        'translate' => $translate,
                        'no_timestamps' => $no_timestamps,
                        'content' => $content,
                    ]);

                    //$filename = self::getClassFilename('App\Models\\' . $classname);

                    if ((!file_exists($filename)) ||
                        (strpos(file_get_contents($filename), '[leave this text to regenerate]') !== false)
                    ) {
                        echo '<b>writing model "' . $classname . '" to "' . $filename . '"</b><br>';

                        file_put_contents($filename, $cdm_template);
                    } else {
                        echo '<b>file "' . $filename . '" already exists! cannot write model "' . $classname . '"</b><br>';
                    }

                }

                /*if (\Schema::hasTable($desc_table)) {

                    $classname = $content_type->model;
                    $description_classname = $classname . 'Description';

                    //if ((class_exists('App\Models\\' . $classname)) && (!class_exists('App\Models\\' . $description_classname))) {

                    if (class_exists('App\Models\\' . $classname)) {
                        $description_filename = self::getClassFilename('App\Models\\' . $classname);
                        $description_filename = before_last('.php', $description_filename) . 'Description.php';
                        if ((!file_exists($description_filename)) ||
                            (strpos(file_get_contents($description_filename), '[leave this text to regenerate]') !== false)
                        ) {

                            $fields = \Schema::getColumnListing($desc_table);
                            $no_timestamps = !(in_array('created_at', $fields) && in_array('updated_at', $fields) && in_array('deleted_at', $fields));

                            $content = '';

                            $classname_namespace = before_last('\\', $classname, 1);

                            // $content_description_model_template
                            $cdm_template = \File::get(storage_path('generator\ContentDescriptionModel.txt'));
                            $cdm_template = bind_string($cdm_template, [
                                'namespace' => $classname_namespace ? 'App\Models\\' . $classname_namespace : 'App\Models',
                                'description_classname' => after_last('\\', $description_classname),
                                'classname' => after_last('\\', $classname),
                                'table' => $content_type->table . '_description',
                                'no_timestamps' => $no_timestamps,
                                'content' => $content,
                            ]);

                            if ((!file_exists($description_filename)) ||
                                (strpos(file_get_contents($description_filename), '[leave this text to regenerate]') !== false)
                            ) {
                                echo '<b>writing model "' . $description_classname . '" to "' . $description_filename . '"</b><br>';

                                file_put_contents($description_filename, $cdm_template);
                            } else {
                                echo '<b>file "' . $description_filename . '" already exists! cannot write model "' . $description_classname . '"</b><br>';
                            }
                        }

                    }
                }*/
            }


            $fields_desc = ContentTypeFields
                ::where('content_type_id', $content_type_k)
                ->orderBy('sort')
                ->get()
                ->keyBy('name')
                ->toArray();

            //$fields_schema = \Schema::getColumnListing($content_type->table);
            $fields_schema = \DB::select('SHOW COLUMNS FROM ' . $content_type->table);
            $fields_schemas = [];
            $fields_schemas[$content_type->table] = $fields_schema;

            $desc_table = $content_type->table . '_description';
            if (\Schema::hasTable($desc_table)) {
                $fields_schemas[$desc_table] = \DB::select('SHOW COLUMNS FROM ' . $desc_table);
            }

            echo '--- Opening ' . $content_type->table . ' ---<br>';

            foreach ($fields_schemas as $fields_schema_k => $fields_schema)
                foreach ($fields_schema as $field_k => $field) {
                    if (!array_key_exists(trim($field->Field), $fields_desc)) {
                        $new_field = $default_field->replicate();
                        $new_field->name = $field->Field;
                        $new_field->sort = -9999;
                        $new_field->content_type_id = $content_type_k;

                        //$new_field->type = $field->Type;

                        if ((($field->Type == 'tinyint(1)') || (starts_with($field->Type, 'int(')))
                            && ends_with($field->Field, '_id')
                            && (\Schema::hasTable(before('_id', $field->Field)))
                        ) {
                            $new_field->type = 'select';
                            $new_field->validators = '';
                            $new_field->form_attributes = '$f->options(\Illuminate\Support\Facades\DB::table("' . before('_id', $field->Field) . '")->pluck("name","id"));';
                        } elseif ($field->Type == 'tinyint(1)') {
                            $new_field->type = 'checkbox';
                            $new_field->validators = 'Integer';
                        } elseif (starts_with($field->Type, 'int(')) {
                            $new_field->type = 'text';
                            $new_field->validators = 'Integer';
                        } elseif (starts_with($field->Type, 'varchar(') || starts_with($field->Type, 'char(')) {
                            $new_field->type = 'textarea';
                            $maxlen = between('(', ')', $field->Type);
                            if ($maxlen)
                                $new_field->validators = 'max:' . $maxlen;
                        } elseif (starts_with($field->Type, 'text')) {
                            $new_field->type = 'redactor';
                            $new_field->validators = '';
                        } elseif ($field->Type == 'datetime') {
                            $new_field->type = 'date';
                            $new_field->validators = '';
                        }


                        $new_field->save();

                        echo 'Added ' . $fields_schema_k . '.' . $field->Field . '<br>';
                    } else {
                        echo 'exists ' . $fields_schema_k . '.' . $field->Field . '<br>';
                    }
                }

        }

        return 1;
    }
}
