<?php

namespace Wbe\Crud\Controllers;

use Wbe\Crud\Models\ModelGenerator;
use Wbe\Crud\Models\Rapyd\FieldsProcessor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Wbe\Crud\Models\ContentTypes\Languages;
use Wbe\Crud\Models\ContentTypes\ContentType;
use Wbe\Crud\Models\ContentTypes\ContentTypeFields;

use Zofe\Rapyd\DataFilter\DataFilter;
use Zofe\Rapyd\DataGrid\DataGrid;
use Zofe\Rapyd\DataEdit\DataEdit;
use Zofe\Rapyd\DataForm\DataForm;


class EditController extends Controller
{
    public function index(Request $r, $content_type, $lang_id = 0)
    {
        if ($r->exists('modify') || $r->exists('insert')) {

            $content = ContentType::find($content_type);
            $content_id = $r->exists('modify') ? $r->input('modify') : false;

            if (!$content) abort('403', 'Content type #' . $content_type . ' not found!');

            // збірка полів на основі описів у crud_content_type_fields та тих, які дійсно наявні у таблиці


            /*foreach ($ct_fields as $field) {
                //if ($field == 'content_id' || $field == 'lang_id') {
                if ($field == 'id' || $field == 'content_id' || $field == 'lang_id') {
                    //do not output, bind later
                } else {
                    if (isset($ct_fields[$field])) {
                        $fields[$field] = $ct_fields[$field];
                        if (starts_with($fields[$field]->Type,'rel:'))
                            $fields[$field]->Type = after('rel:', $fields[$field]->Type);
                    }
                }
            }*/


            /*$fields = [];

            foreach ($fields_schema as $field) {
                //if ($field == 'content_id' || $field == 'lang_id') {
                if ($field == 'id' || $field == 'content_id' || $field == 'lang_id') {
                    //do not output, bind later
                } else {
                    if (isset($ct_fields[$field])) {
                        $fields[$field] = $ct_fields[$field];
                        if (starts_with($fields[$field]->Type,'rel:'))
                            $fields[$field]->Type = after('rel:', $fields[$field]->Type);
                    }
                }
            }
            $fields_desc = [];
            foreach ($fields_desc_schema as $field) {
                //if ($field == 'content_id' || $field == 'lang_id') {
                if ($field == 'id' || $field == 'content_id' || $field == 'lang_id') {
                    //do not output, bind later
                } else {
                    if (isset($ct_fields[$field])) {
                        $fields_desc[$field] = $ct_fields[$field];
                        if (starts_with($fields_desc[$field]->Type,'rel:'))
                            $fields_desc[$field]->Type = after('rel:', $fields[$field]->Type);
                    }
                }
            }*/


            $languages = Languages::all()->pluck('name', 'id');

            //ContentTypeFieldsDescription::where(['lang_id'=>1])->pluck('name','content_type_field_id');


            $content_type_model = /*'App\Models\\' .*/ $content->model;
            if ($r->exists('modify') || $r->exists('content')) {
                $crud_edit = 1;
                //$new_content_type_model = $content_type_model::find($r->input('modify'));
                //if (!$lang_id)
                $new_content_type_model = $content_type_model::where('id', $r->input('modify'))->first();
                if (!$new_content_type_model)
                    $new_content_type_model = new $content_type_model;
                //else
                /*$new_content_type_model = $content_type_model::where([
                    'content_id' => $r->input('modify', $r->input('content')), //strtolower($content->table) . '_id'
                    'lang_id' => $lang_id
                ])->first();*/
                /*if (!$new_content_type_model) {
                    $new_content_type_model = new $content_type_model;
                    //$new_content_type_model->{strtolower($content->table) . '_id'} = $r->input('modify', $r->input('content_id'))
                    $new_content_type_model->id = $r->input('modify', $r->input('content'));
                    $new_content_type_model->lang_id = $lang_id;
                }*/
            } else { //if($r->exists('insert'))
                $crud_add = 1;
                $new_content_type_model = new $content_type_model;
            }


            $desc_table = $content->table . '_description';
            $desc_table_exists = \Schema::hasTable($desc_table);

            $new_content_type_model::saved(function ($row) use ($content, $content_id, $desc_table, $desc_table_exists, $languages) {
                if ($desc_table_exists && isset($_POST[$desc_table])) {
                    $lang_records = \Request::all()[$desc_table];
                    foreach ($lang_records as $post_lang_id => $post_lang) {
                        $lang_records[$post_lang_id]['content_id'] = $row->id;
                        $lang_records[$post_lang_id]['lang_id'] = $post_lang_id;
                    }
                    //print_r($lang_records);

                    foreach ($lang_records as $post_lang_id => $post_lang) {
                        \DB::table($desc_table)->updateOrInsert(['content_id' => $content_id, 'lang_id' => $post_lang_id], $post_lang);
                    }
                }
            });

            $edit = DataForm::source($new_content_type_model);
            $edit->attributes(array("class" => "table table-striped"));

            //if (!$lang_id)
            $edit->label($content->name);
            //else $edit->label(Languages::where('id', $lang_id)->value('name') . ' (' . $lang_id . ')');

            FieldsProcessor::addFields($content, $edit, 'form');

            /*
            $desc_table = $content->table . '_description';
            $desc_table_exists = \Schema::hasTable($desc_table);

            // from crud_content_type_fields
            $ct_fields = ContentTypeFields::getFieldsFromDB($content_type, [['form_show', '=', 1]]);

            $fields_schema = \Schema::getColumnListing($content->table);
            $fields_desc_schema = \Schema::getColumnListing($content->table . '_description');


            foreach ($ct_fields as $field) {
                if ((!in_array($field->name, $fields_desc_schema)) &&
                    (($field->name != 'id') && ($field->name != 'lang_id') && ($field->name != 'content_id'))
                ) {
                    $display = $field->grid_custom_display ? $field->grid_custom_display : $field->name;
                    $f = $edit->add($display, $field->caption ? $field->caption : $field->name, $field->type);
                    if ($field->validators) {
                        $f->rule($field->validators);
                    }
                    if ($field->form_attributes)
                        eval($field->form_attributes);

                    if ($field->relation && (($field->type == 'select') || ($field->type == 'multiselect'))) {
                        if (method_exists($new_content_type_model, $field->relation)) {

                            $model_filename = ContentType::getFilePathByModel($content->model);
                            $rel = ModelGenerator::getModelRelationsMethods(file_get_contents($model_filename), $field->relation);
                            if (isset($rel[2][0])) {
                                $relation_model = new $rel[2][0];

                                if ($relation_model &&
                                    in_array('App\Models\Crud\Translatable', class_uses($relation_model))
                                ) {
                                    $query_builder = $relation_model->translate(session('admin_lang_id'));
                                } else {
                                    $query_builder = $relation_model;
                                }

                                $f->options(array_merge(
                                    [0=>'- Select -'],
                                    $query_builder->pluck(
                                        $field->display_column ? $field->display_column : 'name',
                                        $relation_model->getQualifiedKeyName()
                                    )->toArray()
                                ));
                            } else echo '!isset($rel[2][0]) (model)';
                        } else {
                            $f->options(['no relation "' . $field->relation . '" found']);
                        }
                    }
                }
            }
            //$edit->add('outcomes.name', 'outcomes', 'tags');

            if ($desc_table_exists) {
                $desc_values = collect(\DB::table($desc_table)->where(['content_id' => $content_id])->get())->keyBy('lang_id')->toArray();
                //print_r($desc_values);
                foreach ($ct_fields as $field) {
                    if (($field->name != 'id') && ($field->name != 'lang_id') && ($field->name != 'content_id') &&
                        in_array($field->name, $fields_desc_schema)
                    ) {
                        foreach ($languages as $lang_k => $lang) {
                            //$display = $field->grid_custom_display ? $field->grid_custom_display : $field->name;
                            $f = $edit->add($desc_table . '[' . $lang_k . '][' . $field->name . ']',
                                ($field->caption ? $field->caption : $field->name) . ' (' . $lang . ')',
                                $field->type
                            );
                            if (isset($desc_values[$lang_k]->{$field->name}))
                                $f->value = $desc_values[$lang_k]->{$field->name};
                            //if ($field->validators) {
                            //$f->rule($field->validators);
                            //}
                            if ($field->form_attributes)
                                eval($field->form_attributes);
                        }
                    }
                }
            }*/





            /*$languages = Languages::all()->pluck('name', 'id');
            foreach ($languages as $lang_k => $lang) {
                $edit->add('lang_id', 'LANG_ID', 'text');
            }*/

            /*if ($lang_id){
                //if ($r->exists('modify') || $r->exists('content_id')) {
                    ///$edit->add('content_id', 'Content_id', 'text');
                    $edit->set('content_id', $r->input('content'));
                //}
                $edit->add('lang_id', 'LANG_ID', 'text');
                //$edit->add('id', 'ID', 'text');
                //$edit->set('lang_id', $lang_id);
            } //else echo 'no lang_id';
            */


            //if (!$lang_id) {
            $edit->link(url('admin/crud/grid/' . $content_type . '/'), "Cancel and Back to the grid", "TR");
            $edit->submit('Save', 'BL'); //, ['onclick' => 'form_submit=true;$("iframe.langs").each(function(){$(this).contents().find("form").submit();});return false;']
            //} else $edit->submit('Save', 'BL');

            $edit->saved(function () use ($edit, $content_type, $lang_id) {
                //\Redirect::to(url('admin/'));
                //redirect()->action('Backend\HomeController@index');
                //return redirect('admin/');
                ///die('<meta http-equiv="refresh" content="0; URL=\'' . url('admin/crud/grid/' . $content_type . '/') . '\'" />');

                die('<meta http-equiv="refresh" content="0; URL=\'' . url('admin/crud/grid/' . $content_type . '/') . '\'" />');

                //die('<!DOCTYPE html><html><head><script>alert("hello");</script></head><body></body></html>');

                //$edit->message("record saved");
                //$edit->link(url('admin/crud/grid/' . $content_type . '/'), "Back to the grid");

                //die(view('crud::crud.submitframes'));
                //else
                //\Redirect::to('admin');
                //    $edit->link(url('admin/crud/edit/' . $content_type . '/lang/' . $lang_id . '/?' . $_SERVER['QUERY_STRING']), "Back");

                //redirect('admin/crud/grid/' . $content_type . '/');
            });

            $return = (string)$edit;

            /*$lang_form_urls = [];
            if (!$lang_id) {
                //$languages = [1, 2];
                $lang_form_urls[] = url('/admin/crud/edit/' . $content_type . '/lang/0/?' . $_SERVER['QUERY_STRING']);
                foreach ($languages as $lang_k => $lang) {
                    $lang_form_urls[] = url('/admin/crud/edit/' . $content_type . '/lang/' . $lang_k . '/?' . $_SERVER['QUERY_STRING']);
                }
            }*/

            //$content_name = $content->name;

            return view('crud::crud.form', compact('return', 'lang_id'));


            /*
            $edit = DataForm::source(Hints::where('id',$r->input('modify'))->first());
            $edit->label('Edit Form');
            $edit->link("rapyd-demo/filter", "Articles", "TR")->back();

            $edit->add('hint', 'Hint', 'text');
            $edit->add('market_option', 'Market option', 'text')->rule('numeric');

            $edit->submit('Save');

            $edit->saved(function () use ($edit, $content_type) {
                $edit->message("record saved");
                $edit->link(url('admin/crud/grid/' . $content_type . '/'), "back to the grid");
                //redirect('admin/crud/grid/' . $content_type . '/');
            });

            return view('crud::crud.edit', compact('edit'));*/


            /*$edit = DataEdit::source(Hints::find($r->input('modify')));
            $edit->label('Edit Article');
            $edit->link("rapyd-demo/filter","Articles", "TR")->back();
            $edit->add('title','Title', 'text')->rule('required|min:5');

            $edit->add('body','Body', 'redactor');
            $edit->add('detail.note','Note', 'textarea')->attributes(array('rows'=>2));
            $edit->add('detail.note_tags','Note tags', 'text');
            $edit->add('author_id','Author','select')->options(Author::pluck("firstname", "id")->all());
            $edit->add('publication_date','Date','date')->format('d/m/Y', 'it');
            $edit->add('photo','Photo', 'image')->move('uploads/demo/')->fit(240, 160)->preview(120,80);
            $edit->add('public','Public','checkbox');
            $edit->add('categories.name','Categories','tags');

            return view('crud::crud.edit', compact('edit'));*/
        } elseif ($r->exists('update')) {
            return 'update';
        } elseif ($r->exists('delete')) {
            return 'delete';
        } else abort(404, 'Action not found');
    }
}
