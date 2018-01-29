<?php
namespace  Wbe\Crud\CustomClasses;

use Wbe\Crud\Models\ContentTypes\Languages;
use Wbe\Crud\Models\Globals;
use Wbe\Crud\Models\Meta\Meta;
use  Illuminate\Database\Schema\Blueprint;
use Wbe\Crud\Models\Rapyd\FieldsProcessor;
use Wbe\Crud\Models\ContentTypes\ContentTypeFields;

class MetaSettings
{

    public static $columns=['meta_title','meta_description'];
    private static $table_name_meta = 'crud_settings';
    /**
     * Check if need comments
     * @param $content_type_id
     * @return bool
     */
    public static function NeedMeta($content_type_id){
//        dd(\DB::table('content_type')->where('id','=',$content_type_id)->pluck('need_meta')->first());
        if(\DB::table('content_type')->where('id','=',$content_type_id)->pluck('need_meta')->first()){
            return true;
        }
        else{
            return false;
        }
    }


    /**
     * Add meta columns to table
     *
     * @param $content Content_Type
     * @return object  data
     */
    public function AddMetaFieldsTo($content){
        try{
        if(MetaSettings::NeedMeta($content->id) && $content->is_system ==0){
            $created = false;
            if(MetaSettings::is_description_table($content->table)){
                // add columns to description table
               $created =  $this->columns_create($content->table.'_description');
            }else{
                // add columns to data table
               $created =  $this->columns_create($content->table);
            }
            if($created){
                $settings =  MetaSettings::get_settings_meta($content->id);
                if($settings){
                    return $settings;
                }else{
                    /// create new obj
                    $settings = new Meta;
                    foreach (MetaSettings::$columns as $col){

                        $settings->$col = "";
                    }
                    return $settings;
                }
            }else{
                return false;
            }
        }else{
            // delete meta if exist
            $this->columns_delete($content->table);
            return false;
        }
        }catch (\Exception $ex){
            Globals::$messages[2][] = $ex->getMessage();
        }
        return false;
    }


    /**
     * Chech exist description table
     *
     * @param $table_name table name data
     * @return bool if exist - true  if NOT exist false
     */
    public static function is_description_table($table_name){
        $table_name_desc = $table_name."_description";
        if(\Schema::hasTable($table_name_desc)){
//            dd(true);
            return true;
        }else{
//            dd(false);
            return false;
        }
    }


    /**
     * Save settings
     * @param array $data
     * @param $table_name
     */
    public function add(array $data,$table_name){
        $add =  Meta::where(['content_type_id'=>$data['content_type_id']])->first();
        if(empty($add)){
            $add = new Meta();
            $add->content_type_id  = $data['content_type_id'];
            $add->save();
        }

        if($this->is_description_table($table_name)){ /// for multi lang
            $langs = Languages::pluck('id');
             $this->checkNULL(true,$data,$langs);
            $temp = \DB::table(MetaSettings::$table_name_meta.'_description')->where('content_id','=',$add->id)->get();
            if(empty(count($temp))){/// insert
                foreach ($langs as $lang_id){
                    \DB::table(MetaSettings::$table_name_meta.'_description')
                        ->insert(['content_id'=>$add->id,'lang_id'=>$lang_id,'meta_title'=>$data['meta_title'][$lang_id],'meta_description'=>$data['meta_description'][$lang_id]]);
                }
            }else{ // update
                foreach ($langs as $lang_id){
                    \DB::table(MetaSettings::$table_name_meta.'_description')->where([['content_id','=',$add->id],['lang_id','=',$lang_id]])
                        ->update(['meta_title'=>$data['meta_title'][$lang_id],'meta_description'=>$data['meta_description'][$lang_id]]);
                }
            }
        }else{// one lang
            $temp = \DB::table(MetaSettings::$table_name_meta.'_description')->where('content_id','=',$add->id)->first();
            $this->checkNULL(false,$data);
           if(empty($temp)){// insert
               \DB::table(MetaSettings::$table_name_meta.'_description')
                   ->insert(['content_id'=>$add->id,'lang_id'=>'-1','meta_title'=>$data['meta_title'],'meta_description'=>$data['meta_description']]);
           }else{// update
               \DB::table(MetaSettings::$table_name_meta.'_description')->where([['content_id','=',$temp->id],['lang_id','=','-1']])
                   ->update(['meta_title'=>$data['meta_title'],'meta_description'=>$data['meta_description']]);
           }
        }
    }

    /**
     * Check if null for insert udpate settings
     *
     * @param $description   true or false
     * @param $data   varible with data
     * @param null $langs if $description true need langs
     */
    private function checkNULL($description,$data,$langs = null){
        if($description){
                foreach (MetaSettings::$columns as $col){
                    foreach ($langs as $lang){
                    if(is_null($data[$col][$lang])){
                        $data[$col][$lang] = "";
                    }
                }
            }
        }else{
            foreach (MetaSettings::$columns as $col){
                if(is_null($data[$langs][$col])){
                    $data[$langs][$col] = "";
                }
            }
        }
    }

    private function columns_create($table_name){
        if(\Schema::hasTable($table_name)){
        foreach (MetaSettings::$columns as $column){
            if(!\Schema::hasColumn($table_name, $column)){
                \Schema::table($table_name, function (Blueprint $table)use ($column)
                {
                    $table->text($column);
                });
            }
        }
            return true;
        }else{
            throw new \Exception("not table ".$table_name);
        }
    }

    private function columns_delete($table_name){
        if(MetaSettings::is_description_table($table_name)){
            if (\Schema::hasTable($table_name."_description")) {
                foreach (MetaSettings::$columns as $column) {
                    if (\Schema::hasColumn($table_name."_description", $column)) {
                        \Schema::table($table_name."_description", function (Blueprint $table)use($column) {
                            $table->dropColumn($column);
                        });
                    }
                }
                return true;
            } else {
                throw new \Exception("not table " . $table_name."_description");
            }
        }else {
            if (\Schema::hasTable($table_name)) {
                foreach (MetaSettings::$columns as $column) {
                    if (\Schema::hasColumn($table_name, $column)) {
                        \Schema::table($table_name, function (Blueprint $table) use ($column){
                            $table->dropColumn($column);
                        });
                    }
                }
                return true;
            } else {
                throw new \Exception("not table " . $table_name);
            }
        }
    }


    /**
     * Generate MetaTage
     *
     * @param $content_type_id - content type id
     * @param $lang_id  - curent lang - if need description require
     * @param null $data_description - new description_data
     */
    public static function GenerateMeta($content,$all_data=null,$lang_id=null,$data_description=null){
        if(!MetaSettings::NeedMeta($content->id)){
            if(!empty($data_description)){
                return $data_description;
            }else{
                return $all_data;
            }
        }
//        dump($lang_id);
        $settings = MetaSettings::get_settings_meta($content->id);
            if(MetaSettings::is_description_table($content->table)){
                /// for multi language
                if(is_null($lang_id)){
                    return $all_data;
                }
              $description_settings =   $settings->getDescription($content->table);
              $a = "meta_title";
              foreach(MetaSettings::$columns as $col){
                    if(empty($data_description[$col])) {
                        $data_description[$col] = MetaSettings::replace($col,$description_settings[$lang_id]->$col,$data_description);
                    }
              }
              return $data_description;
        }else{/// for data
            if(MetaSettings::is_description_table($content->table)){
               return $all_data;
            }
            $data_settings = $settings->getDescription();
            foreach(MetaSettings::$columns as $col){
                if(empty($all_data[$col])) {
                     $all_data[$col] = MetaSettings::replace($col,$data_settings[$col],$all_data);
                }
            }
            return $all_data;
        }

    }

    private static function replace($col,$settings,$data){
        preg_match_all("/\[.*?\]/",$settings,$result_arr);
        $temp = $settings;
        foreach($result_arr[0] as $setting){
            $replace = $data[substr($setting,1,-1)];
            $temp = str_replace($setting,$replace,$temp);
        }
        return $temp;
    }

    public static function  set_meta_to_form($content,$filds){
//        dd($content);
        if(MetaSettings::NeedMeta($content->id)) {
            $colection = [];
            foreach (MetaSettings::$columns as $col) {
                $colection[$col] = new ContentTypeFields;
                $colection[$col]->type = "text";
                $colection[$col]->name = $col;
                $colection[$col]->title = $col;
                $filds->put($col, $colection[$col]);
            }
        }
    }

    public static function get_settings_meta($content_type_id){
       $meta =Meta::where('content_type_id','=',$content_type_id)->first();
        return $meta;
    }
}