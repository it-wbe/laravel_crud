<?php

namespace Wbe\Crud\Models\Rapyd;

use App\Models\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Wbe\Crud\Controllers\Rapyd\EditController;
use Wbe\Crud\Models\ContentTypes\Languages;
use Wbe\Crud\Models\ContentTypes\ContentType;
use Wbe\Crud\Models\ContentTypes\ContentTypeFields;
use Wbe\Crud\Models\ModelGenerator;
use Wbe\Crud\Models\Rapyd\FieldsProcessor;
use Zofe\Rapyd\DataFilter\DataFilter;
use Zofe\Rapyd\DataGrid\DataGrid;
use Zofe\Rapyd\DataEdit\DataEdit;
use Zofe\Rapyd\DataForm\DataForm;

class FieldsProcessor
{

    /// attr tab
    /// value 0 - data
    /// value 1 - description
    /// value 2 - relations type


    /// what content tabs we need
    ///
    public static  $cont_tabs = [];
    public static $needTab = [];
    /**
     * Створення Rapyd полів на основі записів у crud_content_type_fields ($rapyd->add)
     * @param $content ContentType
     * @param $rapyd DataForm
     * @param $type string filter|table
     * @request Requset
     * @return bool
     */
    static public function addFields($content,  $rapyd, $type,$show=false)
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
        // для фільтра: відображати багатострічкові тектові поля в одну стрічку
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
//        // додавання полів
        foreach ($ct_fields as $field) {
            if ((!in_array($field->name, $fields_desc_schema)) &&
                (($type != 'filter') || in_array($field->name, $fields_schema)) &&
                (($field->name != 'id') && ($field->name != 'lang_id') && ($field->name != 'content_id'))
            ) {
                if($field->type == 'Wbe\Crud\Models\Rapyd\Fields\Relation')
                {
                    $display = $field->name;
                    // dd($field);
                    $f = $rapyd->add($display, $field->title != "not set" ? $field->title : $field->name, $field->type,$rapyd->model,$rapyd->model->getRelations());
                    $cont_type_id = ContentType::where('table',$field->name)->pluck('id')->first();
                    $contentRelationFildsType = ContentTypeFields::getFieldsFromDB($cont_type_id, [['form_show', '=', 1]]);
                    $contentRelationFilds =  \Schema::getColumnListing($field->name);
                    $contentRelationFildsDesc = \Schema::getColumnListing($field->name.'_description');
                    // dd($contentRelationFilds);
                    $rules = [];
                    foreach ($contentRelationFildsType as $value) {
                        if(in_array($value->name,$contentRelationFilds)){
                            if($value->name!='id'){
                                $rules[$field->name.'.*.'.$value->name] = $value->validators;
                            }
                        }
                        if(in_array($value->name,$contentRelationFildsDesc)){
                            $rules[$field->name.'_description.*.*.'.$value->name] = $value->validators;
                        }
                    }
                    $f->rule($rules);
                    $f->form = $rapyd;
                    $f->attributes['tab'] =2;
                    FieldsProcessor::$cont_tabs[2] = true;
                }else {
                    $display = $field->name;
                    if($type == 'filter' &&$field->type == 'number') {
                        $field->type = 'text';
                        $f = $rapyd->add($display, $field->title != "not set" ? $field->title : $field->name, $field->type);
                        $f->clause = 'custom';
//                      dd($f);
                    }else {
//                      dd($content->table);
                        if ($field->type == "image" || $field->type == "file") {
                            if (\Request::input('process')) {
                                if(!is_null(\Request::file($field->name)))
                                    $filename = \Request::file($field->name)->getClientOriginalName();
                            }else{$filename ='';}

                            //todo fix hard core $parh File::exists
                            $f = $rapyd->add($display, $field->title != "not set" ? $field->title : $field->name, $field->type)
                                ->move('files/'.$content->table)
                                ->webpath(public_path());
								$f->attributes['class']=" ";
                        } else{

                            $f = $rapyd->add($display, $field->title != "not set" ? $field->title : $field->name, $field->type);
							$f->attributes['class']=" ";
                        }

                    }
//                 dd($f->attributes['tab']);
                    $f->attributes['tab']=0;
                    FieldsProcessor::$cont_tabs[0] = true;
                    if ($field->validators) {
//                        $f->rule($field->validators);
                    }
                }
            }
        }
        if ($desc_table_exists) {
            $desc_values = collect(\DB::table($desc_table)->where(['content_id' => $content->rec_id])->get())->keyBy('lang_id')->toArray();
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
//            index для масиву needTab
            $index_need_tab = 0;

            /// generate description fields
            foreach ($ct_fields as $field) {
                if (($field->name != 'id') && ($field->name != 'lang_id') && ($field->name != 'content_id') &&
                    in_array($field->name, $fields_desc_schema)
                ) {
                    FieldsProcessor::$needTab[$index_need_tab] = [];
                    foreach ($languages as $lang_k => $lang) {
                        $field_key = $desc_table . '[' . $lang_k . '][' . $field->name . ']';
                        if ($field->type == "image" || $field->type == "file") {
                            if (\Request::input('process')) {
                                $filename = \Request::file($field->name)->getClientOriginalName();
                            }else{$filename ='';}
                            //todo fix hard core $parh File::exists
                            $rapyd->add(
                                $field_key,
                                ($field->caption ? $field->caption : $field->name) . ' (' . $lang . ')',
                                $field->type
                            )->move('files/'.$content->table)->webpath(public_path());
//                            $f->path = 'public/';
                        }
                        else{
                            $rapyd->add(
                                $field_key,
                                ($field->caption ? $field->caption : $field->name) . ' (' . $lang . ')',
                                $field->type
                            );
                        }

                        if (($type != 'filter') && isset($desc_values[$lang_k]->{$field->name})){
                                $rapyd->fields[$field_key]->value = $desc_values[$lang_k]->{$field->name};
                        }
                        $rapyd->fields[$field_key]->attributes['tab'] =1;
                        FieldsProcessor::$cont_tabs[1] = true;
                        FieldsProcessor::$needTab[$index_need_tab][] = $field_key;
                    }
                    $index_need_tab++;
                }
            }
        }

        // пост-обробка доданих полів
        foreach ($rapyd->fields as $f_name => $f){
//            dd($ct_fields);
            if (isset($ct_fields[$f_name])) {
//                dd($ct_fields[$f_name]);
                $field = $ct_fields[$f_name];
                if ($field->form_attributes) {
                    eval($field->form_attributes);
                }
//                if($show&&$field->type == 'image'||$field->type == 'file'){
////                    dd($f);
////                    $f->value = ('asdasd');
//                    $f->old_value = $f->value;
////                    dd($f);
//                }
                // тип поля "select": заповнення
                if ($field->relation && ($field->type == 'select')) {
                        $model_filename = ContentType::getFilePathByModel($content->model);
                        $rel = ModelGenerator::getModelRelationsMethods(file_get_contents($model_filename), $field->relation);
                        if (isset($rel[2][0])) {
                            $relation_model = new $rel[2][0];
                            $options = $relation_model->pluck(
                            $field->display_column ? $field->display_column : 'name',
                                $relation_model->getQualifiedKeyName()
                            )->toArray();
                            unset($options[count($options)+2]);
//                            dd($options);
                            array_unshift($options,"- Select -");
                            $f->options($options);
                        } else echo 'Relation not found! (' . $field->relation . ')';
                    // тип поля "tags": задяння ajax обробника
                } elseif ($field->relation && ($field->type == 'tags')) {
                    $model_filename = ContentType::getFilePathByModel($content->model);
                    $rel = ModelGenerator::getModelRelationsMethods(file_get_contents($model_filename), $field->relation);
                    if (!isset($rel[2][0]))
                        return 'error';
                    $relation_model = $rel[2][0];
                    $f->remote(
                        null, null,
                        '/admin/autocomplete/' . str_replace('\\', '_', $relation_model) . '/' . $field->search_columns . '/10/'
                    );
                }
            }
        }
        return true;
    }
}
