<?php

namespace Wbe\Crud\Models\Rapyd;

use Wbe\Crud\Models\ContentTypes\Languages;
use Wbe\Crud\Models\ContentTypes\ContentType;
use Wbe\Crud\Models\ContentTypes\ContentTypeFields;
use Wbe\Crud\Models\ModelGenerator;
use Zofe\Rapyd\DataFilter\DataFilter;
use Zofe\Rapyd\DataGrid\DataGrid;
use Zofe\Rapyd\DataEdit\DataEdit;
use Zofe\Rapyd\DataForm\DataForm;

class FieldsProcessor
{
    /**
     * Збірка полів на основі описів у crud_content_type_fields та тих, які дійсно наявні у таблиці
     * @param $content
     * @param $rapyd
     * @param $type string filter|table
     * @return null
     */
    static public function addFields($content, DataForm $rapyd, $type)
    {
        $desc_table = $content->table . '_description';
        $desc_table_exists = \Schema::hasTable($desc_table);

        // from crud_content_type_fields
        switch ($type) {
            case 'filter':
                $where = [['grid_filter', '=', 1]];
                break;
            case 'form':
                $where = [['form_show', '=', 1]];
                break;
            default:
                die('addFields: unknown type "' . $type . '"');
        }

        $ct_fields = ContentTypeFields::getFieldsFromDB($content->id, $where);

        $fields_schema = \Schema::getColumnListing($content->table);
        $fields_desc_schema = \Schema::getColumnListing($content->table . '_description');


        if ($type == 'filter') {
            foreach ($ct_fields as $ct_field_k => $ct_field) {
                if (isset($ct_fields[$ct_field_k])) {

                    switch ($ct_field->type) {
                        case 'redactor':
                        case 'Wbe\Crud\Models\Rapyd\Fields\Ckeditor':
                        case 'textarea':
                            $ct_fields[$ct_field_k]->type = 'text';
                            break;
                    }

                } else echo '!isset $ct_fields[' . $ct_field_k . ']';
            }
        }

        foreach ($ct_fields as $field) {
            if ((!in_array($field->name, $fields_desc_schema)) &&
                (($type != 'filter') || in_array($field->name, $fields_schema)) &&
                (($field->name != 'id') && ($field->name != 'lang_id') && ($field->name != 'content_id'))
            ) {
                $display = $field->grid_custom_display ? $field->grid_custom_display : $field->name;

                $f = $rapyd->add($display, $field->caption ? $field->caption : $field->name, $field->type);

                if ($field->validators) {
                    $f->rule($field->validators);
                }

                //$f->db_name = $field->name;
            }
        }

        if ($desc_table_exists) {
            $desc_values = collect(\DB::table($desc_table)->where(['content_id' => $content->rec_id])->get())->keyBy('lang_id')->toArray();
            //print_r($desc_values);

            switch ($type) {
                case 'filter':
                    $languages = Languages::where('id', session('admin_lang_id'))->pluck('name', 'id');
                    break;
                case 'form':
                    $languages = Languages::all()->pluck('name', 'id');
                    break;
            }
            if (!isset($languages))
                die('unknown type!');


            foreach ($ct_fields as $field) {
                if (($field->name != 'id') && ($field->name != 'lang_id') && ($field->name != 'content_id') &&
                    in_array($field->name, $fields_desc_schema)
                ) {
                    //echo $field->name;
                    foreach ($languages as $lang_k => $lang) {
                        //$display = $field->grid_custom_display ? $field->grid_custom_display : $field->name;

                        //echo $desc_table . '[' . $lang_k . '][' . $field->name . '] ; '. $field->type;

                        $field_key = $desc_table . '[' . $lang_k . '][' . $field->name . ']';

                        $rapyd->add(
                            $field_key,
                            ($field->caption ? $field->caption : $field->name) . ' (' . $lang . ')',
                            $field->type
                        );

                        if (($type != 'filter') && isset($desc_values[$lang_k]->{$field->name}))
                            $rapyd->fields[$field_key]->value = $desc_values[$lang_k]->{$field->name};

                        //$f = $rapyd->fields[$field_key];

                        //if (($type != 'filter') && isset($desc_values[$lang_k]->{$field->name}))
                        //$f->value = '$desc_values[$lang_k]->{$field->name}';
                        //$f->attributes(['value'=>'123']);

                        ///echo $desc_values[$lang_k]->{$field->name};

                        //if ($field->validators) {
                        //   $f->rule($field->validators);
                        //}

                        //$f->db_name = $field->name;
                    }
                }
            }
        }


        foreach ($rapyd->fields as $f_name => $f)
            if (isset($ct_fields[$f_name])) {
                $field = $ct_fields[$f_name];
                /*if ($field->validators) {
                    //echo $field->validators;
                    $rapyd->fields[$f_name]->rule($field->validators);
                    //echo '"'.$field->validators.'"';
                }*/
                if ($field->form_attributes) {
                    //echo $field->form_attributes;
                    eval($field->form_attributes);
                }


                /*
                 * Обробка Rapyd полів перед виводом
                 * */
                if ($field->relation && ($field->type == 'select')) {
                    //if (method_exists($data_source, $field->relation))
                    //{

                    $model_filename = ContentType::getFilePathByModel($content->model);
                    $rel = ModelGenerator::getModelRelationsMethods(file_get_contents($model_filename), $field->relation);
                    if (isset($rel[2][0])) {
                        $relation_model = new $rel[2][0];

                        /*if ($relation_model &&
                            in_array('App\Models\Crud\Translatable', class_uses($relation_model))
                        ) {
                            $query_builder = $relation_model->translate(session('admin_lang_id'));
                        } else {
                            $query_builder = $relation_model;
                        }*/

                        $f->options(array_merge(
                            [0 => '- Select -'],
                            $relation_model->pluck(
                                $field->display_column ? $field->display_column : 'name',
                                $relation_model->getQualifiedKeyName()
                            )->toArray()
                        ));
                    } else echo '!isset($rel[2][0]) (model)';
                    //} else {
                    //    $f->options(['no relation "' . $field->relation . '" found']);
                    //}
                } elseif ($field->relation && ($field->type == 'tags')) {
                    $model_filename = ContentType::getFilePathByModel($content->model);
                    $rel = ModelGenerator::getModelRelationsMethods(file_get_contents($model_filename), $field->relation);
                    if (!isset($rel[2][0]))
                        return 'error';
                    $relation_model = $rel[2][0];

                    //print_r($rel);
                    //echo (new $relation_model)->getQualifiedKeyName();

                    $f->remote(
                        //"name", 'id',
                        null, null,
                        '/admin/autocomplete/' . str_replace('\\', '_', $relation_model) . '/' . $field->search_columns . '/10/'
                    );
                    //$f->
                }
                //$f->build();
            } //else echo '!isset $rapyd->fields[' . $f_name . ']';

        //$rapyd->add('1','1','App\Models\Crud\Rapyd\Fields\Ckeditor');
        //$rapyd->add('test','test tags','tags')->remote("name", "id", "/admin/autocomplete/Crud_User/0/0/"); //. urldecode($content->model) . "");
        //$form->add('users.email','Users','tags')->remote("fullname", "id", "/admin/users/autocomplete");

    }
}