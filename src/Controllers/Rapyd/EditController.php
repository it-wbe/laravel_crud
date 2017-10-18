<?php

namespace Wbe\Crud\Controllers\Rapyd;

use App\Models\Relation;
use Mockery\Exception;
use Wbe\Crud\Models\ModelGenerator;
use Wbe\Crud\Models\Rapyd\FieldsProcessor;

use App\Models\ContentTypes\Markets;
use App\Models\ContentTypes\News;

use App\Models\ContentTypes\Outcome;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Wbe\Crud\Models\ContentTypes\Languages;
use Wbe\Crud\Models\ContentTypes\ContentType;
use Wbe\Crud\Models\ContentTypes\ContentTypeFields;

use Illuminate\Support\Facades\Input;

use Zofe\Rapyd\DataFilter\DataFilter;
use Zofe\Rapyd\DataGrid\DataGrid;
use Zofe\Rapyd\DataEdit\DataEdit;
use Zofe\Rapyd\DataForm\DataForm;
use Zofe\Rapyd\Rapyd;
use Validator;


class EditController extends Controller
{
   static public $request;
    /**
     * Форма редагування запису поточного типу контенту, його видалення
     * @param Request $r
     * @param $content_type
     * @param int $lang_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View|string
     */
    public function index(Request $r, $content_type, $lang_id = 0)
    {
      // dd(\App\Models\Portfolios::find(7)->relations()->get());
      // dump($r->session());
        EditController::$request = $r;
        if ($r->exists('modify') || $r->exists('insert')) {
//            dd($content_type);
            $content = ContentType::find($content_type);
//            dd($content);
            if (!$content)
                die('content type with id ' . $content_type . ' not found');

            $content_id = $r->exists('modify') ? $r->input('modify') : false;
            $content->rec_id = $content_id;

            if (!$content) abort('403', 'Content type #' . $content_type . ' not found!');

//            dd($content);
//            $model->relations
            //$content_model = 'App\Models\\' . $content->model;
            $content_model = $content::getCTModel($content->model);
//             dump($content_model);
//            dd(\Schema::getColumnListing($content->table)[0], $r->input('modify'));
  // dd($content_model);
            if (!$content_model)
                die('model not found: ' . $content->model);

            if ($r->exists('modify') || $r->exists('content')) {
                if(\Schema::hasColumn($content->table, 'id')) {
//                    dump('first');
//                    dump($content_model->with($content_model->relationships)->all());
                    $new_content_model = $content_model::where('id', $r->input('modify'))->first();
                } else {
//                    dump('else');
                    $new_content_model = $content_model::where(\Schema::getColumnListing($content->table)[0], $r->input('modify'))->first();
                  //  dd($content_model);
                }
                if (!$new_content_model)
//                    dump('if !new content model');
                    $new_content_model = new $content_model;
            } else {
//                dump('new content model');
                $new_content_model = (new $content_model);
            }
            $desc_table = $content->table . '_description';
            $desc_table_exists = \Schema::hasTable($desc_table);
            try {
                $new_content_model::saved(function ($row) use ($content, $content_id, $desc_table, $desc_table_exists) {
                    $this_contentFilds = ContentTypeFields::getFieldsFromDB($content->id, [['form_show', 1]]);
                    foreach ($this_contentFilds as $filds) {
                        if ($filds->type == 'Wbe\Crud\Models\Rapyd\Fields\Relation') {
                            $languages = Languages::all()->pluck('name', 'id');
                            $contentTypeId = ContentType::where('table', $filds->name)->pluck('id')->first();
//                        $this->contentFields = \Schema::getColumnListing($filds->name);
                            $desc_table = $filds->name . '_description';
                            $contentFieldsDescription = \Schema::getColumnListing($filds->desc_table);
                            $tableFieldsType = ContentTypeFields::getFieldsFromDB($contentTypeId, [['form_show', '=', 1]]);
//                        dd($tableFieldsType );
                            /// наш новый тип достаем поля типа делаем новые обекты модельки
                            $contentFields = \Schema::getColumnListing($filds->name);
                            $tableName = $filds->name;
                            $all_id = \DB::table('ct_to_relations')
                                ->where([
                                    ['ct_to_relations_id', '=', $row->id],
                                    ['ct_to_relations_type', '=', get_class($row)]])
                                ->pluck('relations_id')->toArray();
                            if (isset($_POST[$tableName])) {
                                foreach (Input::get($tableName) as $key_rel => $item) {
                                    $update = false;
                                    foreach ($row->$tableName()->get() as $rel) {
                                        /// видаляемо id з массиву які є на сторінці , інші вважаємо видаленими
                                        if (isset($item['id']) && $rel->id == (int)$item['id']) {
                                            $search_id = array_search($rel->id, $all_id);
                                            unset($all_id[$search_id]);
                                            $update = true;
                                            $item = $this->fillmodel($item, $contentFields, $tableFieldsType, $tableName);
                                            if ($item['id'] < 0) {
                                                $item['id'] = null;
                                            }
                                            $rel->fill($item);
                                            $rel->save();
                                        }
                                    }
                                    $desc = null;
                                    if (!$update) {
                                        /// create new
                                        $item = $this->fillmodel($item, $contentFields, $tableFieldsType, $tableName);
                                        if ($item['id'] < 0) {
                                            $item['id'] = null;
//                                        unset($item['id']);
                                        }
//dd($item);
//                                    dd($tableName);
//                                    dd($row->$tableName()->);
//                                    $newInstance  = \Wbe\Crud\Models\ContentTypes\Relations::create($item);
                                        $newInstance = $row->$tableName()->create($item);
//                                    dd($item);
                                        $new_typeInstance['id'] = $newInstance->id;
                                        $newInstance->save();
                                    }
                                    $desc_table_cont = $tableName . '_description';
                                    $desc_table_cont_exists_rel = \Schema::hasTable($desc_table);
                                    /// save ower type description
                                    /// content id

                                    if (!isset($new_typeInstance)) {
                                        $key = $key_rel;
                                    } else {
                                        $key = $new_typeInstance['id'];
                                    }
                                    $desc = Input::get($tableName . '_description')[$key_rel];
                                    if (!is_null($desc)) {
                                        if ($desc_table_cont_exists_rel && isset($_POST[$desc_table_cont])) {

                                            $lang_records = $desc;
                                            foreach ($desc as $post_lang_id => $post_lang) {

                                                if (!isset($new_typeInstance)) {
                                                    $lang_records[$post_lang_id]['content_id'] = $key_rel;
                                                } else {
                                                    $lang_records[$post_lang_id]['content_id'] = $new_typeInstance['id'];
                                                }

                                                $lang_records[$post_lang_id]['lang_id'] = $post_lang_id;
                                            }
                                            foreach ($lang_records as $post_lang_id => $post_lang) {
                                                \DB::table($desc_table_cont)->updateOrInsert(['content_id' => $key, 'lang_id' => $post_lang_id], $post_lang);
                                            }
                                        }
                                    }
                                }
                                //// remove all
//                            dump($all_id);
                            }/// relation взагалі ніякого нема  /// remova relations
//                        dd($all_id);
//                        $aa = array_values($all_id);
//                        dd($aa);
                            foreach ($all_id as $id) {
                                \DB::table($filds->name)->where('id', '=', $id)->delete();
                            }
//                                 remove description end relations table
                            foreach ($all_id as $value) {
                                \DB::table($filds->name . '_description')->where('content_id', '=', $value)->delete();

                                \DB::table('ct_to_relations')->where([
                                    ['relations_id', '=', $value],
                                    ['ct_to_relations_type', '=', get_class($row)]])->delete();
                            }
                        }
                    }
                    $desc_table = $content->table . '_description';
//                dd($desc_table);

                    // сохранение description для модели
//                dd($_POST[$desc_table]);
                    if ($desc_table_exists && isset($_POST[$desc_table])) {

                        $lang_records = \Request::all()[$desc_table];
//                    dd($desc_table);
                        foreach ($lang_records as $post_lang_id => $post_lang) {
                            $lang_records[$post_lang_id]['content_id'] = $row->id;
                            $lang_records[$post_lang_id]['lang_id'] = $post_lang_id;
                        }

                        foreach ($lang_records as $post_lang_id => $post_lang) {
//                        dd($lang_records);/
                            try{
                                \DB::table($desc_table)->updateOrInsert(['content_id' => $content_id, 'lang_id' => $post_lang_id], $post_lang);
                             }catch (\Exception $ex){
                                EditController::$request->session()->flash('message.level', 'danger');
                                EditController::$request->session()->flash('message.content', 'Error!'.$ex->getMessage());
                            }
                        }
                    }
                });
            }catch (\Exception $ex){
                EditController::$request->session()->flash('message.level', 'danger');
                EditController::$request->session()->flash('message.content', 'Error!'.$ex->getMessage());
            }
            // var_export($new_content_model);
            // die;
            $edit = DataForm::source($new_content_model);

            $edit->attributes(array("class" => "table table-striped"));
            // method lable
            $lable_name_method ='';
            if($r->exists('insert')){
                $lable_name_method =     trans('crud::common.content_add');
            }
            elseif($r->exists('modify'))
            {
                $lable_name_method = trans('crud::common.content_edit');
            }
                $edit->label($content->name . ' > ' .  $lable_name_method);
//            dump($content);
            FieldsProcessor::addFields($content, $edit, 'form');
            /// $tab  - for description tabs
           $tab = FieldsProcessor::$needTab;

           /// $cont_tab - for content tube $key is id content like data description or relation value is boot
            $cont_tab = FieldsProcessor::$cont_tabs;
            ksort($cont_tab);
            $edit->link(url('admin/crud/grid/' . $content_type . '/'), trans('crud::common.cancel'), "TR");
            $edit->submit('Save', 'TR');
//           try {
               $edit->saved(function () use ($edit, $content_type, $lang_id) {
//                $new_conte
//                dump('saved not foreach');
//                dd($edit->model->id);//->Model->id);
                   //\Redirect::to(url('admin/'));
                   //redirect()->action('Backend\HomeController@index');
                   //return redirect('admin/');
                   ///die('<meta http-equiv="refresh" content="0; URL=\'' . url('admin/crud/grid/' . $content_type . '/') . '\'" />');

                if (\Request::has('to'))
                    $back_url = \Request::input('to');
                else{
                    if(config('crud.edit_redirect')==1){
                        $back_url = url('admin/crud/edit/' . $content_type . '?modify='.$edit->model->id);
                    }else{
                    $back_url = url('admin/crud/grid/' . $content_type . '/');
                    }
                }
////
                header('Location: ' . $back_url);
                die();
//                return redirect();
//                   $edit->link(url('admin/crud/grid/' . $content_type . '/'), "save", "TR");
//                   $edit->submit('Save', 'TR');
                   //                   return back();
                   //die('<meta http-equiv="refresh" content="0; URL=\'' . $back_url . '\'" />');
               });
//           }
//           catch (\Exception $ex){
//                EditController::$request->session()->flash('message.level', 'danger');
//                EditController::$request->session()->flash('message.content', 'Error!'.$ex->getMessage());
////                EditController::$request->flash('error', 'Save was unsuccessful!');
//            }

            $edit->build();
//            dump($edit);
            return view('crud::crud.form', compact('edit', 'lang_id','tab','cont_tab'));

        } elseif ($r->exists('delete')) {

            $content = ContentType::find($content_type);
            $content_model = /*'App\Models\\' .*/ $content->model;
            $new_content_model = $content_model::where('id', $r->input('delete'))->first();
                if (!$new_content_model)
                    return 'content type does not exists';
//            dd($new_content_model->);
            $desc_table_cont = $content->name. '_description';
            $desc_table_cont_exists_rel = \Schema::hasTable($desc_table_cont);
            if($desc_table_cont_exists_rel )
            {
                \DB::table($new_content_model['table'].'_description')->where('content_id','=',$r->input('delete'))->delete();
            }
            $new_content_model->delete();

            return redirect(url('admin/crud/grid/' . $content_type . '/'));
        } else abort(404, 'Action not found');
    }

    public function fillmodel($item ,$contentFilds,$contentFildsType,$modelName){
        // наполняем контент
//        dump($item);
        $newItem = [];
        foreach ($contentFilds as $content_key =>$content_value){
//            dd($item[$content_value]);
//            $id = $item[$content_value];
//            $id--;
//            dd(EditController::$request->file([$modelName])[$id][$content_value]['val']);
//            dd(EditController::$request->file([$modelName]));
//            dd($item);
            //dd($content_key);
//               $file = EditController::$request->file([$modelName])[$content_key][$value->name]['val'];
//            dd($contentFildsType[$content_value]->type);
            $newItem[$content_value] = $this->getValue($item,$content_value,$modelName,$contentFildsType[$content_value]->type);
        }

        return $newItem;
    }

    public function getValue($item,$content_value,$modelName,$type){
        if($type!= 'image'){
            if(is_array($item[$content_value])){
              return  implode('|', $item[$content_value]);
            }else{
                return $item[$content_value];
            }
        }
        else{
            $id = $item['id'];
            // картинка може бути завантажена тільки що
            if(isset(EditController::$request->file([$modelName])[$id][$content_value]['val'])){
               $file =  EditController::$request->file([$modelName])[$id][$content_value]['val'];
                return '/files/' . $modelName. '/'.$file->getClientOriginalName();
            }
            // значення в нас знаходиться в назві поля old_img
            return $item[$content_value]['old_img'];
        }
    }
}
