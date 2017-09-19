<?php

namespace Wbe\Crud\Models\Rapyd\Fields;

use Mockery\Exception;
use Zofe\Rapyd\Helpers\HTML;
use Zofe\Rapyd\Rapyd;
use Collective\Html\FormFacade as Form;
use Wbe\Crud\Models\Rapyd\FieldsProcessor;
use Zofe\Rapyd\DataForm\Field;
use Wbe\Crud\Controllers\Rapyd\EditController;
use Illuminate\Support\Facades\File;
use Wbe\Crud\Models\Rapyd\Fields\Ckeditor;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Input;
use Wbe\Crud\Models\ContentTypes\ContentType;
use Wbe\Crud\Models\ContentTypes\ContentTypeFields;
use Wbe\Crud\Models\ContentTypes\Languages;
use Illuminate\Support\Facades\View;

class Relation extends \Zofe\Rapyd\DataForm\Field\Field
{
  public $type='relation';
  public $hidden=false;
  public $multiple = true;
  public $values;
  public $contentFields;
  public $contentFieldsDescription;
  public $tableFieldsType;
  public $form;
  public $request_refill = false;
  public $request;
  // public $mode = 'readonly';

  public function getNewValue()
  {
      $val = [];

      if (!is_null(Input::get($this->name))) {
          foreach (Input::get($this->name) as $input_key =>$input) {
//              dd($input_key);
              //// проходимся по всему что пришло и если файл или картинка загружаем
              foreach ($this->tableFieldsType as $value) {
                  // делаем главный контент
                  if (in_array($value->name, $this->contentFields)) {
                      if (isset($input[$value->name])) {
                           $this->loadField($value, $input, $val,$input_key);
                      }
                  } //  загружаем  description контент
                  elseif (in_array($value->name, $this->contentFieldsDescription)) {

                       $this->loadField($value, $input, $val,$input_key,true);

                  }
              }
            $this->new_value[] = $val;
          }
      }


  }

  public function loadField($value,$input,&$val,$input_key,$description = false)
  {
      if ($value->type != 'image' && $value->type != 'file') {
          if (isset($input[$value->name])) {
              if (is_array($input[$value->name])) {// значение состоит из 2
                  if ($description) {
                      $val['desc'][$value->name] = implode('|', $input[$value->name]);
                  } else {
                      $val[$value->name] = implode('|', $input[$value->name]);
                  }
              } else { // у нас простое значение
                  if ($description) {
                      $val['desc'][$value->name] = $input[$value->name];
                  } else {
                      $val[$value->name] = $input[$value->name];
                  }
              }
          }
      }// загружаем картинку
      else {
          $old_file = true;
          if(isset(EditController::$request->file([$this->name])[$input_key][$value->name]['val'])) {
              // удаляем старую картинку на сервере
              $file = EditController::$request->file([$this->name])[$input_key][$value->name]['val'];
              if(!is_null($file)&&!is_null($input[$value->name]['old_img'])){
//                  dd('delete');
                  $a = $input[$value->name]['old_img'];
                   File::delete(base_path().$a);
              }
              /// загружаем файл
              $file_origin_name = $file->getClientOriginalName();
              $img_val = '/files/'.$this->name.'/'.$file_origin_name;
              if(!file_exists(public_path().'/files/'.$this->name.'/'.$file_origin_name)) {
                  $file->move(public_path() . '/files/' . $this->name . '/', $file_origin_name);
              }
              $old_file = false;
          }
          if (!is_null($input[$value->name]['old_img'])&&$old_file!=false) {
              $img_val = $input[$value->name]['old_img'];
          }
          if ($description) {
              $val['desc'][$value->name] = $img_val;
          } else {
              $val[$value->name] = $img_val;
          }
      }
  }
  public function rule($rules){
    foreach ($rules as $key => $value) {
      $this->rule[$key] = $value;
    }
  }

  public function getValue(){
      $process = (Input::get('search') || Input::get('save')) ? true : false;
    $this->languages = Languages::all()->pluck('name', 'id');
    $cont_type =  ContentType::where('table',$this->name)->first();
    $this->contentFields = \Schema::getColumnListing($this->name);
    $this->desc_table = $this->name.'_description';
    $this->contentFieldsDescription = \Schema::getColumnListing($this->desc_table);
      $contentTypeId = $cont_type->id;
//      dd($cont_type);
    $this->tableFieldsType =  ContentTypeFields::getFieldsFromDB($contentTypeId, [['form_show', '=', 1]]);
//    $this->request =
      $temp_val=null;
      if($process){
          $this->getNewValue();
          $temp_val = $this->new_value;
      }else {
          $rel = $this->name;
          $temp_val = $this->model->$rel;
      }
//      dd($this->contentFields);
      $temp_val [] =  new $cont_type->model;// (new \App\Models\Relations);
      foreach ($temp_val as $val) {
        // передаем поля рапиду
        $this->value[] =  $this->CreateFilds($val);
    }
//        dump($this->value);
      $this->old_value = $this->value;

//      dump(Input::get());
//      if($this->form->validator)
//   dump($this->form->validator->errors());
  }

  public function CreateFilds($val){
      $val_field = [];
      /// создание нормального вида главного контента
      foreach ($this->tableFieldsType as $key => $value) {
          if(in_array($key,$this->contentFields)){
              $val_field[$key]['val'] = isset($val[$key])?$val[$key]:null;
              $val_field[$key]['type'] = $value->type;
          }
      }
      /// создание полей для таблицы description
      if(isset($val->id)){
          $val_field['desc'] = $desc_values = collect(\DB::table($this->desc_table)->where(['content_id' => $val->id])->get())->keyBy('lang_id')->toArray();
      }else{
          /// создаем поля descrirption для нового поля
          foreach ($this->languages as $lang_key => $lang_value) {
              $lang = new \stdClass;
              foreach ($this->contentFieldsDescription as $desc_key => $desc_value) {
                  $lang->$desc_value =$value[$desc_value];
              }
              $val_field['desc'][$lang_key] =$lang;
          }
      }
      // add types for _description
      if(reset($val_field['desc']))
          foreach (reset($val_field['desc']) as $key => $value) {
              if(isset($this->tableFieldsType->toArray()[$key])){
                  $val_field['desc']['types'][$key] =$this->tableFieldsType->toArray()[$key]['type'];
              }
          }
          return $val_field;
  }


  public function build()
  {
      $output='';
      if (parent::build() === false)
          return;
      switch ($this->status) {
          case "disabled":
          case "show":
              break;
          case "create":
          case "insert":
          case "modify":
          $output .='<style type="text/css">.relations{ border: 1px solid #C4C4C4; margin: 10px; padding: 20px;}</style>';
          $output.= '<a class="btn btn-info pull-right relation_add"><span class="glyphicon glyphicon-plus"></span></a>';
          $output.= $this->createOutPut();
          Rapyd::js('relation/relation.js');
            break;
          case "hidden":
              $output = Form::hidden($this->name, $this->value);
              break;

          default:;
      }
       $this->output = "\n" . $output . "\n" . $this->extra_output . "\n";
  }
  protected function createOutPut(){
    $output='';
      ///// generate filds
      /// по нашим данным
      $i = 1;
      $message =null;
      foreach ($this->value as $val_key => $val_value) {
          $filds = [];

//          dd($this->value);
          // проходим по полям
          foreach ($val_value as $key => $value) {
              if($key != 'desc') { /// проходим по главным полям
                  $id = '';
                  if (!isset($val_value['id']['val'])) {
                      // если у записи нет id
                      $id = -$i;
                  } else {
                      // если у записи есть id
                      $id = $val_value['id']['val'];
                  }
                  if ($key != 'id') {
                      $type= $value['type'];
                  } else {
                      $type = 'hidden';
                  }
//                  dd($key ,$value,$id,$this->name);
                  if($type!='image'){
                      $message_key = $this->name . '.' . $id . '.' . $key;
                      $fild_name = $this->name . '[' . $id . ']' . '[' . $key . ']';
                  }
                  else{
                      $message_key = $this->name . '.' . $id . '.' . $key;
                      $fild_name = $this->name . '[' . $id . ']' . '[' . $key . '][val]';
                  }
//                  dd($fild_name );
                  $fild_id = $this->name . $id . $key;
//                  dd($value['val']);
                  if($key == 'id'&& $id<0){
                      $temp_fild  = $this->createfieldToType($fild_name, $key, $id,$type , $fild_id);
                  }else{
                      $temp_fild  = $this->createfieldToType($fild_name, $key, $value['val'],$type , $fild_id);
                  }
                  if(!is_null($this->form->validator)){
                      $errors = $this->form->validator->errors();
                      $temp_fild->message = $errors->first($message_key);
                  }
                  $filds[] = $temp_fild;
              }
              else{ /// проходим по полям _description
                  $desc = [];
                  /// по языкам
                  foreach ($this->languages as $lang_key => $lang_value) {
                      $desc_filds= [];
                      if(isset($value['types']))
                          foreach ($value['types'] as $desc_key => $desc_value) {
                              $desc_val = $val_value['desc'][$lang_key]->$desc_key;
                              $field_name_description = $this->name.'_description['.$id.']'.'['.$lang_key.']'.'['.$desc_key.']';
                              $message_key = $this->name.'_description.'.$id.'.'.$lang_key.'.'.$desc_key;
                              $temp_fild = $this->createfieldToType($field_name_description, $desc_key,$desc_val ,$desc_value,$message_key);
                              if(!is_null($this->form->validator)){
                                  $errors = $this->form->validator->errors();
                                  $temp_fild->message = $errors->first($message_key);
                              }
                              $desc_filds[$desc_key] = $temp_fild;
                          }
                      $desc[$lang_key] = $desc_filds;
                  }
              }
          }
//          dd('asd');
          $i++;
          $filds['desc'] = $desc;
          if($val_value ===end($this->value)){
              // display none;
              $display = false;
          }
          else{
              // display show;
              $display = true;
          }
//            dd($id);
          $output .=View::make('crud::rapyd.fild')->with('langs',$this->languages)->with('filds',$filds)->with('id',$id)->with('display',$display);
      }
      return $output;
  }


  protected function createfieldToType($name, $lable,$value , $type,$id){
      switch (strtolower($type)){
          case 'colorpicker':
           $out = new  Field\Colorpicker($name,$lable);
              break;
          case 'date':
              $out = new Field\Date($name,$lable);
              break;
          case 'daterange':
              $out = new Field\Daterange($name,$lable);
              break;
          case 'datetime':
              $out = new Field\Datetime($name,$lable);
              break;
          case 'file':
              $out = new Field\File($name,$lable);
              break;
          case 'image':
              $out = new \Wbe\Crud\Models\Rapyd\Fields\Image($name,$lable);
//              dump($value);
//              'enctype'=>'multipart/form-data'
              $this->form->attributes['enctype'] = 'multipart/form-data';
              break;
          case 'wbe\crud\models\rapyd\fields\ckeditor':
              $out = new \Wbe\Crud\Models\Rapyd\Fields\Ckeditor($name,$lable);
              break;
          case 'checkbox':
              $out = new Field\Checkbox($name,$lable);
              break;
          case 'checkboxgroup':
              $out = new Field\Checkboxgroup($name,$lable);
              break;
          case 'hidden':
              $out = new Field\Hidden($name,$lable);
              break;
          case 'password':
              $out = new Field\Password($name,$lable);
              break;
          case 'select':
              $out = new Field\Select($name,$lable);
              break;
          case 'tags':
              $out = new Field\Tags($name,$lable);
              break;
          case 'text':
              $out = new Field\Text($name,$lable);
              break;
          case 'textarea':
              $out = new Field\Textarea($name,$lable);
              break;
          case 'number':
              $out = new Field\Number($name,$lable);
              break;
      }
      if(!is_null($out)) {
          $out->value = $value;
//          if($out->type='image'||$out->type='file'){
////              dd($value);
//              $out->old_value =$value;
////              dd(asqq);
////              dd($out->old_value );
//          }
//          $out->value= $value;
//dump($value);
//          $out->value= $value;
          $out->attributes['id'] = $id;
          $out->status='create';
          $out->build();
      }
      return $out;
  }
}
